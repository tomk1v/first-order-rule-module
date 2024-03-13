<?php
/**
 * First Order Rule
 * Model rule adds executing.
 *
 * @category Internship
 * @package Internship\FirstOrderRule
 * @author Andrii Tomkiv <tomkivandrii18@gmail.com>
 * @copyright 2024 Tomkiv
 */

namespace Intership\FirstOrderRule\Model\Rule\Condition;

class Order extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Config\Model\Config\Source\Yesno $sourceYesno
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        protected \Magento\Rule\Model\Condition\Context $context,
        protected \Magento\Config\Model\Config\Source\Yesno $sourceYesno,
        protected \Magento\Customer\Model\Session $session,
        protected \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        protected \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Load attribute options.
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'customer_first_order' => __('Is customer first order')
        ]);
        return $this;
    }

    /**
     * Get an input type.
     *
     * @return string
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * Get value element type.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Get value select options.
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData(
                'value_select_options',
                $this->sourceYesno->toOptionArray()
            );
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Customer First Order Rule Condition.
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     * @throws \Exception
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $firstOrder = 0;
        if ($this->session->isLoggedIn()) {
            try {
                $customerId =  $this->session->getCustomerId();
                $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId)->create();
                $order = $this->orderRepository->getList($searchCriteria)->getFirstItem();
                if (!$order->getId()) {
                    $firstOrder = 1;
                }
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage(), $exception->getCode());
            }
        }
        $model->setData('customer_first_order', $firstOrder);
        return parent::validate($model);
    }
}
