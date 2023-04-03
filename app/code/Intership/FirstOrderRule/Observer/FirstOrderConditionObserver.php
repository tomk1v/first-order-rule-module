<?php
/**
 * First Order Rule
 * Observer for adding 'First Order' rule.
 *
 * @category  Internship
 * @package   Internship\FirstOrderRule
 * @author    Andrii Tomkiv <tomkivandrii18@gmail.com>
 * @copyright 2023 Tomkiv
 */

namespace Intership\FirstOrderRule\Observer;

class FirstOrderConditionObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Path of xml coupon value.
     */
    const XML_ENABLED_VALUE = 'first_order_rule/general/enable';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * If the module is enabled add a new cart rule.
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isEnabled = $this->scopeConfig->getValue(self::XML_ENABLED_VALUE);
        if ($isEnabled) {
            try {
                $additional = $observer->getAdditional();
                $conditions = (array) $additional->getConditions();

                $conditions = array_merge_recursive($conditions, [
                    $this->getCustomerFirstOrderCondition()
                ]);
                $additional->setConditions($conditions);
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage(), $exception->getCode());
            }
        }
        return $this;
    }

    /**
     * Get condition for customer first order.
     * @return array
     */
    private function getCustomerFirstOrderCondition()
    {
        return [
            'label'=> __('Is customer first order'),
            'value'=> \Intership\FirstOrderRule\Model\Rule\Condition\Order::class
        ];
    }
}
