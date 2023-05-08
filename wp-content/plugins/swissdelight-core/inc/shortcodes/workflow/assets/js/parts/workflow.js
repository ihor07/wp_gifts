(function ($) {
    "use strict";

    qodefCore.shortcodes.swissdelight_core_workflow = {};

    $(document).ready(function () {
        qodefWorkflow.init();
    });

    var qodefWorkflow = {
        init: function () {
            this.holder = $('.qodef-workflow');
            if (this.holder.length) {
                this.holder.each(function () {
                    var workflowShortcode = $(this);
                    if (workflowShortcode.hasClass('qodef-workflow-animate')) {
                        var workflowItems = workflowShortcode.find('.qodef-workflow-item');

                        workflowShortcode.appear(function () {
                            workflowShortcode.addClass('qodef-appeared');
                        }, {accX: 0, accY: -100});

                        workflowItems.each(function (i) {
                            var workflowItem = $(this);
                            workflowItem.appear(function () {
                                setTimeout(function(){
                                    workflowItem.addClass('qodef-appeared');
                                },100);
                            });
                        }, {accX: 0, accY: 0});

                    }
                });
            }
        },
    };

    qodefCore.shortcodes.swissdelight_core_workflow.qodefWorkflow  = qodefWorkflow;

})(jQuery);