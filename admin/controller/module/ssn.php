<?php
class ControllerModuleSsn extends Controller {
    private $error = array();

    protected $_options = array(
        'ssn_payex_status',
        'ssn_payex_account_number',
        'ssn_payex_encryption_key',
        'ssn_payex_mode'
    );

    public function index() {
        $this->language->load('module/ssn');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!is_writable(DIR_APPLICATION . 'view/javascript/common.js')) {
                $this->error['warning'] = $this->language->get('error_writable');
                return;
            }

            // Inject JS Code
            $contents = file_get_contents(DIR_CATALOG . 'view/javascript/common.js');
            $ssn_contents = file_get_contents(DIR_CATALOG . 'view/javascript/ssn.js');
            $is_injected = (utf8_strpos($contents, 'Social Security Number Module') !== false);
            if ($this->request->post['ssn_payex_status'] == 1) {
                if (!$is_injected) {
                    file_put_contents(DIR_CATALOG . 'view/javascript/common.js', $contents . "\n\n" . $ssn_contents);
                }
            } else {
                if ($is_injected) {
                    // Remove injected code
                    $contents = preg_replace("!//Start SSN Block(.*?)//End SSN Block!si","", $contents);
                    file_put_contents(DIR_CATALOG . 'view/javascript/common.js', $contents);
                }
            }

            // Save Settings
            $data = array();
            foreach ($this->_options as $option) {
                $data[$option] = $this->request->post[$option];
            }
            $this->model_setting_setting->editSetting('ssn', $data);

            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_status'] = $this->language->get('text_status');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_account_number'] = $this->language->get('text_account_number');
        $this->data['text_encryption_key'] = $this->language->get('text_encryption_key');
        $this->data['text_mode'] = $this->language->get('text_mode');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('module/welcome', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('module/ssn', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('extension/ssn', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['token'] = $this->session->data['token'];

        // Load options
        foreach ($this->_options as $option) {
            if (isset($this->request->post[$option])) {
                $this->data[$option] = $this->request->post[$option];
            } else {
                $this->data[$option] = $this->config->get($option);
            }
        }

        $this->template = 'module/ssn.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'module/ssn')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
?>