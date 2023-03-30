<?php

namespace Intership\FirstOrderCoupon\Model\Rule\Condition;

use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Api\OrderRepositoryInterface;

class Order extends \Magento\Rule\Model\Condition\AbstractCondition
{
    //TODO: Code refactoring, adding comments

    /**
     * @var Yesno
     */
    protected Yesno $sourceYesno;

    /**
     * @var Session
     */
    protected Session $session;

    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param Yesno $sourceYesno
     * @param Session $session
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context                  $context,
        Yesno                    $sourceYesno,
        Session                  $session,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder    $searchCriteriaBuilder,
        array                    $data = []
    ) {
        parent::__construct($context, $data);
        $this->sourceYesno = $sourceYesno;
        $this->session = $session;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Load attribute options
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'customer_first_order' => __('Customer first order')
        ]);
        return $this;
    }

    /**
     * Get input type
     * @return string
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * Get value element type
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Get value select options
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
     * Validate Customer First Order Rule Condition
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
