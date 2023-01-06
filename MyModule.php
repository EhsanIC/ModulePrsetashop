<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class MyModule extends Module
{

    public function __construct()
    {
        $this->name = 'MyModule';
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
            && $this->registerHook('displayHeader')
            && $this->regsiterHook('displayHome')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('MyModule', 'my friend')
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

            $configValue = (string) Tools::getValue('MYMODULE_CONFIG');

            if (empty($configValue) || !Validate::isGenericName($configValue)) {

                $output = $this->displayError($this->l('invalid configuration Value'));
            } else {

                Configuration::updateValue('MYMODULE_CONFIG', $configValue);
                $output = $this->displayConfirmation($this->l('setting updated'));
            }
        }

        return $output . $this->displayForm();
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
                        'type' => 'text',
                        'label' => 'configuration',
                        'name' => 'MYMODULE_CONFIG',
                        'size' => 20,
                        'required' => true,
                    ]
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
        $helper->fields_value['MYMODULE_CONFIG'] = Tools::getValue('MYMODULE_CONFIG', Configuration::get('MYMODULE_CONFIG'));

        return $helper->generateForm([$form]);
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->context()->smarty()->assign([
            'my_module_name' => Configuration::get('MYMODULE'),
            'my_module_link' => $this->context()->link()->geLinkModule('mymodule', 'display')
        ]);

        return $this->display(__FILE__, 'mymodule.tpl');
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayLeftColumn($params);
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
    //         'test' => Counfiguration::get('MYMODULE_CONFIG')
    //     ]);

    //     return $this->display(__FILE__,'test.tpl');
    // }

}
