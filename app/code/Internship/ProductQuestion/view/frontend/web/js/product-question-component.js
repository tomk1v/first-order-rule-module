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
            if (validateFormQuestion()) {
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
                $("#product-question-modal").modal("closeModal");
            }
        });

        function validateFormQuestion() {
            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const question = $('#question').val().trim();

            if (name == null || name === "") {
                $(".name-hint").show();
                return false
            }
            if (email == null || email === "") {
                $(".email-hint").show();
                return false
            }
            if (question == null || question === "") {
                $(".question-hint").show();
                return false
            }

            return true;
        }
    });
