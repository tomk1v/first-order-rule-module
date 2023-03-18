require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function(
        $,
        modal
    ) {
        let options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            buttons: [{
                text: $.mage.__('Close'),
                class: 'product-question-modal',
                click: function () {
                    this.closeModal();
                }
            }]
        };

        let productQuestion = modal(options, $('#product-question-modal'));

        $("#question-button").on('click',function(){
            $("#product-question-modal").modal("openModal");
        });

        $("#submit-question").on('click',function(){
            $.ajax({
                url: '/product/email/question',
                showLoader: true,
                data: {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    question: $('#question').val()
                },
                dataType : 'json',
                type: 'POST'
            });
        });
    });
