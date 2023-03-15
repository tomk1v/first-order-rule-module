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
                text: $.mage.__('Continue'),
                class: 'productQuestion',
                click: function () {
                    this.closeModal();
                }
            }]
        };
        let productQuestion = modal(options, $('#productQuestion'));
        $("#questionButton").on('click',function(){
            $("#productQuestion").modal("openModal");
        });
    });
