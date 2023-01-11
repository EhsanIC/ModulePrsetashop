<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class MyModule extends Module
{
    public $fileds = [
        'MyModuleActive',
        'MyModuleTitle',//
        'MyModulePosition',
        'MyModuleColor',//
        'MyModuleDescription',//
    ];

    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'other';
        $this->version = '1.0.0';
        $this->author = 'me';
        /** 
         * find in translate (need_instance)
         */

        $this->need_instance = 0;
        $this->ps_version_compliancy = [
            'min' => '1.6.0',
            'max' => '1.7.9'
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'my module';
        $this->description = 'module description';
        $this->confirmUninstall = $this->l('delete ? ');

        if (!configuration::get('MyModule')) {
            $this->warning = $this->l('no name');
        }
    }

    public function install()
    {
        if (shop::isFeatureActive()) {
            shop::setContext(shop::CONTEXT_ALL);
        }

        return (parent::install()
            && $this->registerHook('displayLeftColumn')
            && $this->registerHook('displayTop')
            && $this->registerHook('displayHome')
            && Configuration::updateValue('MyModuleTitle', 'my friend')
        );
    }

    public function Uninstall()
    {
        return (parent::uninstall()
            && Configuration::deleteByName('MyModule')
        );
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {

            $configValue = (string) Tools::getValue('MyModuleTitle');

            if (empty($configValue) || !Validate::isGenericName($configValue)) {
                $output = $this->displayError($this->l('invalid configuration Value'));
            } else {
                // Load current value into the form
                foreach ($this->fileds as $filed) {
                    Configuration::updateValue($filed, Tools::getValue($filed), true);
                }

                $output = $this->displayConfirmation($this->l('setting updated'));
            }
        }

        return  $output . $this->displayForm();
    }

    public function displayForm()
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('settings')
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => 'فعال باشد؟',
                        'name' => 'MyModuleActive',
                        'values' => [
                            [
                                'id' => 'type_switch_on',
                                'value' => 1,
                            ],
                            [
                                'id' => 'type_switch_off',
                                'value' => 0,
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => 'configuration',
                        'name' => 'MyModuleTitle',
                        'size' => 20,
                        'required' => true,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'مکان',
                        'name' => 'MyModulePosition',
                        'options' => [
                            'query' => [
                                [
                                    'name' 	=> 'صفحه اصلی',
                                    'hook'	=> 'displayHome',
                                ],
                                [
                                    'name' 	=> 'ستون سمت چپ',
                                    'hook'	=> 'displayLeftColumn',
                                ],
                                [
                                    'name' 	=> 'هدر',
                                    'hook'	=> 'displayTop',
                                ],
                            ],
                            'id' => 'hook',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'color',
                        'label' => 'input color',
                        'name' => 'MyModuleColor',
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'text area with rich text editor',
                        'name' => 'MyModuleDescription',
                        'autoload_rte' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('save'),
                    'class' => 'btn btn-default pull-right'
                ]
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        foreach ($this->fileds as $filed) {
            $helper->fields_value[$filed] = Tools::getValue($filed, Configuration::get($filed));
        }

        return $helper->generateForm([$form]);
    }

    public function displayBlock($params, $hookName = false){
        if (empty(Configuration::get('MyModuleActive'))) {
            return false;
        }

        if (Configuration::get('MyModulePosition') != $hookName) {
            return false;
        }

        // get
        $vars = Configuration::getMultiple($this->fileds);

        // process

        // display smarty
        $this->context->smarty->assign($vars);
        return $this->display(__FILE__, 'mymodule.tpl');
    }

    public function hookDisplayHome($params) {
        return $this->displayBlock($params, 'displayHome');
    }

    public function hookDisplayLeftColumn($params) {
        return $this->displayBlock($params, 'displayLeftColumn');
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->displayBlock($params, 'displayRightColumn');
    }



    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'mymodule-style',
            $this->_path . 'views/css/css.css',
            [
                'media' => 'all',
                'priority' => 1000
            ]
        );

        $this->context->controller->registerJavaScript(
            'mymodule-javascript',
            $this->_path . 'views/js/mymodule.js',
            [
                'position' => 'bottom',
                'priority' => 1000
            ]
        );
    }

        /**
        * متغییری که به tpl ارسال میشه از کجا میاد
        */
    // public function hookDisplayHome($params)
    // {
    //     $this->context->smarty->assign([
    //         'test' => Counfiguration::get('MyModuleTitle')
    //     ]);

    //     return $this->display(__FILE__,'test.tpl');
    // }

}
