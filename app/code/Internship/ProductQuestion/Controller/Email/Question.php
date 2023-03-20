<?php

namespace Internship\ProductQuestion\Controller\Email;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;

class Question implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $transportBuilder;

    /**
     * @var Escaper
     */
    protected Escaper $escaper;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManager;

    /**
     * @var ResultFactory
     */
    protected ResultFactory $resultFactory;

    /**
     * @var Validator
     */
    protected Validator $formKeyValidator;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * Email constructor.
     * @param Context $context
     * @param ManagerInterface $messageManager
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param ResultFactory $resultFactory
     * @param Validator $formKeyValidator
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        ResultFactory $resultFactory,
        Validator $formKeyValidator,
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request
    ){
        $this->messageManager = $messageManager;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->resultFactory = $resultFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * @return ManagerInterface
     */
    public function execute()
    {
        $request = $this->request->getParam('question');
        if(!$request) {
            return $this->messageManager->addErrorMessage('Invalid form key');
        }

        $emailTempVariables['product'] = '$product';
        $senderName = $this->scopeConfig->getValue(
            'trans_email/ident_custom1/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $senderEmail = $this->scopeConfig->getValue(
            'trans_email/ident_custom1/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $receiver = $senderEmail;
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($emailTempVariables);
        $sender = [
            'name' => $senderName,
            'email' => $senderEmail,
        ];

        try {
            $transport = $this->transportBuilder->setTemplateIdentifier('email_product_question')
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                ->setTemplateVars([
                    'name' => $this->request->getParam('name'),
                    'email' => $this->request->getParam('email'),
                    'question' => $this->request->getParam('question')
                ])
                ->setFromByScope($sender)
                ->addTo($receiver)
                ->setReplyTo($receiver)
                ->getTransport();
            $transport->sendMessage();
            return $this->messageManager->addSuccessMessage(__('We have received your message'));
        } catch (\Exception $exception) {
            return $this->messageManager->addErrorMessage(__('Something went wrong'));
        }
    }
}
