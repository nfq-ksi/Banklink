PHP library for integrating various Baltic banks internet services (payment, authentication)
==============

![Build Status](https://travis-ci.org/Shmarkus/Banklink.svg?branch=master)

# Installation
Ensure that you have openssl and mbstring extensions enabled in php.ini
```properties
extension=mbstring
extension=openssl
```

# Usage
```php
class Banklink {
    /**
     * Method prepares each bank protocols and initialises bank list attribute
     */
    function prepareLinks() {
        $httpHttps = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        $returnUrl = $httpHttps . '://' . $_SERVER['HTTP_HOST'] . '/pangalink?bank=seb'; 
        $protocol = new \Banklink\Protocol\iPizza(
            'seller-id', 
            'seller-name', 
            'seller-account-number', 
            'path/to/private_key.pem',
            'path/to/public_key.pem',
            $returnUrl
        );
        $this->banks['seb'] = new \Banklink\SEB($protocol);
    }
    
    /**
     * Method prints out bank links HTML forms for each bank initialised in
     * prepareLinks method. Use the output on your page as a hidden form. 
     * Submit the form to the bank (something like $('#seb').submit())
     * 
     * @param long $orderId            
     * @param double $sum            
     * @param string $description
     * 
     * @return string HTML forms (hidden) for bank transactions
     */
    public function printForm ($orderId, $sum, $description) {
        $forms = '';
        foreach ($this->banks as $name => $bank) {
            $request = $bank->preparePaymentRequest($orderId, $sum, $description);
            $forms .= sprintf(
                    "<form id='%s' method='post' action='%s'>%s</form>", $name, 
                    $request->getRequestUrl(), $request->buildRequestHtml());
        }
        return $forms;
    }
    
    /**
     * Handle bank response and return response object
     * 
     * @param string[] $response HTTP POST
     * @param string bank name
     * 
     * @return \Banklink\Response\Response
     * @throws \InvalidArgumentException
     */
    public function parseBankResponse($response, $bank) {
        return $this->banks[$bank]->handleResponse($response);
    }
}
```

The previous is all the basics you need to prepare the payment. Once submitted to the bank, parse the response:
```php
/**
 * Process response from bank and when response is positive, return 1, otherwise return -1
 *
 * @param string[] $response
 *            HTTP GET and POST (nordea uses GET to reply, others POST)
 */
function processResponse ($response)
{
    $bankLink = new Banklink();
    if (isset($response['VK_SERVICE']) || isset($response['SOLOPMT_RETURN_VERSION'])) { // check if this is bank postback
        $response = $bankLink->parseBankResponse($response, $_GET['bank']);
        if ($response->isSuccesful()) {
			// Do something with the successful response
        }
    }
	return -1;
}
```
