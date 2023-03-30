<?php

namespace Intership\FirstOrderCoupon\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class CustomerConditionObserver
 */
class FirstOrderConditionObserver implements \Magento\Framework\Event\ObserverInterface
{
    const XML_ENABLED_VALUE = 'first_order_coupon/general/enable';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Execute observer.
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
            'value'=> \Intership\FirstOrderCoupon\Model\Rule\Condition\Order::class
        ];
    }
}
