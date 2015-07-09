<?php
if (!defined('_PS_VERSION_'))
    exit;

class ConfirmBasketNotEmpty extends Module
{
    public function __construct()
    {
        $this->name = 'confirmbasketnotempty';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Enzo Hamelin';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6'); 

        parent::__construct();

        $this->displayName = $this->l('Confirm basket not empty');
        $this->description = $this->l('Module to display a message when the user want to quit and his basket is not empty.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Confirm basket not empyt ?');
         
        if (!Configuration::get('CONFIRMBASKETNOTEMPTY_MESSAGE'))      
            $this->warning = $this->l('No message provided');
    }

    public function install()
    {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);

        if (!parent::install() ||
        !$this->registerHook('footer') ||
        !$this->registerHook('header') ||
        !Configuration::updateValue('CONFIRMBASKETNOTEMPTY_MESSAGE', 'Etes vous sur de vouloir quitter ? Il y a des articles dans votre panier.')
        )
            return false;
 
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
        !Configuration::deleteByName('CONFIRMBASKETNOTEMPTY_MESSAGE')
        )
            return false;
 
        return true;
    }
    
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addJs($this->_path.'confirmbasketnotempty.js');
        $this->context->controller->addCSS($this->_path.'confirmbasketnotempty.css');
    }

    public function hookDisplayFooter($params)
    {
        $this->context->smarty->assign(array(
            'confirmbasketnotempty_message' => Configuration::get('CONFIRMBASKETNOTEMPTY_MESSAGE')
        ));
        return $this->display(__FILE__, 'confirmbasketnotempty.tpl');
    }

    public function getContent()
    {
        $output = null;
 
        if (Tools::isSubmit('submit'.$this->name)) {
            $message = strval(Tools::getValue('CONFIRMBASKETNOTEMPTY_MESSAGE'));
            if (!$message  || empty($message) || !Validate::isGenericName($message))
                $output .= $this->displayError( $this->l('Invalid Configuration value') );
            else {
                Configuration::updateValue('CONFIRMBASKETNOTEMPTY_MESSAGE', $message);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
     
        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Message value'),
                    'name' => 'CONFIRMBASKETNOTEMPTY_MESSAGE',
                    'size' => 200,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
     
        $helper = new HelperForm();
     
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
     
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
     
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
     
        // Load current value
        $helper->fields_value['CONFIRMBASKETNOTEMPTY_MESSAGE'] = Configuration::get('CONFIRMBASKETNOTEMPTY_MESSAGE');
     
        return $helper->generateForm($fields_form);
    }
}
?>