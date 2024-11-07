# Magento 2 First Order Rule Module

### Description
The First Order Rule module is a custom Magento 2 module that introduces a new cart price rule condition. This condition allows merchants to create rules based on whether a customer is placing their first order.

### Key Features: 
Customer First Order Condition: Adds a condition to cart price rules to check if the customer is placing their first order.

Observer: An observer checks if the module is enabled and adds the custom rule condition to the existing cart price rules.

### Installation
1. Prepare the Module Directory: <br/>
`mkdir -p app/code/Intership/FirstOrderRule`

2. Clone the Repository: <br/>
`git clone https://github.com/tomk1v/first-order-rule-module.git`

2. Run the following commands from the Magento root directory: <br/>
`bin/magento module:enable Intership_FirstOrderRule` <br/>
`bin/magento setup:upgrade` <br/>
`bin/magento setup:di:compile` <br/>

3. Reindex & Flush the cache: <br/>
`bin/magento cache:flush`

### Usage
Navigate to Stores -> Configuration -> Internship -> First Order Rule and turn on the module:
![image](https://user-images.githubusercontent.com/91790934/234280192-267518dd-3258-483c-a1c0-1ba5db93e905.png)

Go to Marketing -> Promotions -> Cart Price Rules, and look for the First Order Rule under the list of available rules.
![image](https://user-images.githubusercontent.com/91790934/234281451-000df1c8-94f8-45ed-9fdd-6281269ff345.png)
![image](https://user-images.githubusercontent.com/91790934/234281580-f809b250-ed86-4a2d-8b01-4a2233844e8c.png)

Enjoy.
![2024-03-13_16-36](https://github.com/tomk1v/first-order-rule-module/assets/91790934/b0b6048e-0901-4f39-829b-7bba6372ec0d)

### Compatibility

This module is designed to work seamlessly with:

Magento 2.4.6 <br/>
PHP 8.2 <br/>
©tomk1v
