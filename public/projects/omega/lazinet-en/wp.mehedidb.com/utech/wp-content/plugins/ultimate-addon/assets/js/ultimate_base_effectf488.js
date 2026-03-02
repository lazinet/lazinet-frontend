;(function ($) {
    // Run this code under Elementor.
    $(window).on('elementor/frontend/init', function () {
        var FloatingFx = elementorModules.frontend.handlers.Base.extend({
            onInit: function () {
                elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
                this.run();
            },

            getTheElement: function () {
                return this.$element.find('.elementor-widget-container')[0];
            },

            resetFx: function () {
                anime.remove(this.getTheElement());
                this.getTheElement() && this.getTheElement().removeAttribute('style');
            },

            onDestroy: function () {
                elementorModules.frontend.handlers.Base.prototype.onDestroy.apply(this, arguments);
                this.resetFx();
            },

            onElementChange: function () {
                this.resetFx();
                this.run();
            },

            run: function () {
                var settings = this.getElementSettings(),
                    fxSettings = {
                        targets: this.getTheElement(),
                        loop: true,
                        direction: 'alternate',
                        easing: 'easeInOutSine'
                    };

                //console.log(settings);

                if (settings.ultimate_floating_fx_translate_toggle) {
                    if (settings.ultimate_floating_fx_translate_x.size) {
                        fxSettings.translateX = {
                            value: settings.ultimate_floating_fx_translate_x.size,
                            duration: settings.ultimate_floating_fx_translate_duration.size,
                            delay: settings.ultimate_floating_fx_translate_delay.size || 0
                        }
                    }
                    if (settings.ultimate_floating_fx_translate_y.size) {
                        fxSettings.translateY = {
                            value: settings.ultimate_floating_fx_translate_y.size,
                            duration: settings.ultimate_floating_fx_translate_duration.size,
                            delay: settings.ultimate_floating_fx_translate_delay.size || 0
                        }
                    }
                }

                if (settings.ultimate_floating_fx_rotate_toggle) {
                    if (settings.ultimate_floating_fx_rotate_x.size) {
                        fxSettings.rotateX = {
                            value: settings.ultimate_floating_fx_rotate_x.size,
                            duration: settings.ultimate_floating_fx_rotate_duration.size,
                            delay: settings.ultimate_floating_fx_rotate_delay.size || 0
                        }
                    }
                    if (settings.ultimate_floating_fx_rotate_y.size) {
                        fxSettings.rotateY = {
                            value: settings.ultimate_floating_fx_rotate_y.size,
                            duration: settings.ultimate_floating_fx_rotate_duration.size,
                            delay: settings.ultimate_floating_fx_rotate_delay.size || 0
                        }
                    }
                    if (settings.ultimate_floating_fx_rotate_z.size) {
                        fxSettings.rotateZ = {
                            value: settings.ultimate_floating_fx_rotate_z.size,
                            duration: settings.ultimate_floating_fx_rotate_duration.size,
                            delay: settings.ultimate_floating_fx_rotate_delay.size || 0
                        }
                    }
                }

                if (settings.ultimate_floating_fx_scale_toggle) {
                    if (settings.ultimate_floating_fx_scale_x.size) {
                        fxSettings.scaleX = {
                            value: settings.ultimate_floating_fx_scale_x.size,
                            duration: settings.ultimate_floating_fx_scale_duration.size,
                            delay: settings.ultimate_floating_fx_scale_delay.size || 0
                        }
                    }
                    if (settings.ultimate_floating_fx_scale_y.size) {
                        fxSettings.scaleY = {
                            value: settings.ultimate_floating_fx_scale_y.size,
                            duration: settings.ultimate_floating_fx_scale_duration.size,
                            delay: settings.ultimate_floating_fx_scale_delay.size || 0
                        }
                    }
                }

                if (settings.ultimate_floating_fx_translate_toggle || settings.ultimate_floating_fx_rotate_toggle || settings.ultimate_floating_fx_scale_toggle) {
                    this.getTheElement() && this.getTheElement().style.setProperty('will-change', 'transform');
                    anime(fxSettings);
                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            new FloatingFx({
                $element: $scope
            });
        });
    });
}(jQuery));