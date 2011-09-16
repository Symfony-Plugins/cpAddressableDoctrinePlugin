<?php

class Doctrine_Template_Addressable extends Doctrine_Template {
  /**
   * Array of Addressable options
   *
   * @var string
   */
  protected $_options = array('update_table_definition' => true,
                              'home_country' => 'FR',
                              'default_culture' => 'fr',
                              'city_line_layout' => array('CA' => 'city|state|zip',
                                                          'FR' => 'zip|city',
                                                          'HK' => 'city',
                                                          'UK' => 'city|zip',
                                                          'US' => 'city|state|zip',
                                                          'default' => 'city|zip'),
                              'ignore_home_country' => true);

    /**
     * Set table definition for Addressable behavior
     *
     * @return void
     */
    public function setTableDefinition() {
      if ($this->_options['update_table_definition']) {
        $this->hasColumn('address1', 'string', 36);
        $this->hasColumn('address2', 'string', 36);
        $this->hasColumn('city', 'string', 36);
        $this->hasColumn('zip', 'string', 20);
        $this->hasColumn('state', 'string', 10);
        $this->hasColumn('country_id', 'string', 2, array( 'default' => $this->_options['home_country']));
      }
    }

  public function cityAndCountryId($ignore_home_country = true) {
    $s = $this->getInvoker()->getCity();
    if (!$ignore_home_country || 
          $this->_options['home_country'] != $this->getInvoker()->getCountryId()) {
      $s .= ' [' . $this->getInvoker()->getCountryId() . ']';
    }
    return $s;
  } 

  protected function addressCityLine($culture = null) {
    $city = $this->getInvoker()->getCity();
    $zip = $this->getInvoker()->getZip();
    $state = $this->getInvoker()->getState();
    $country_id = $this->getInvoker()->getCountryId();
    $layout = (isset($this->_options['city_line_layout'][$country_id]) ? 
                 $this->_options['city_line_layout'][$country_id] :
                 $this->_options['city_line_layout']['default']);
    if (!$layout) { $layout = $this->_options['city_line_layout'][$this->_options['home_country']]; }
    if (!$layout) { $layout = $this->_options['city_line_layout']['default']; }
    
    $tokens = explode('|', $layout);
    $values = array();
    foreach ($tokens as $token) { $values[] = $$token; }
    return implode(' ', $values);
  }

  public function addressLines($culture = null) {
    $address = array($this->getInvoker()->getAddress1());
    $address2 = $this->getInvoker()->getAddress2();
    if ($address2) { $address[] = $address2; }
    $address[] = $this->addressCityLine();
    if (!$this->_options['ignore_home_country'] ||
          $this->getInvoker()->getCountryId() != $this->_options['home_country']) {
      $address[] = $this->getI18n()->getCountry($this->getInvoker()->getCountryId(), $culture);
    }
    return $address;
  }

  public function address($separator, $culture = null) {
    return implode($separator, $this->addressLines($culture));
  }

  public function addressHtml($culture = null) {
    return $this->address('<br/>');
  }

  public function addressText($culture = null) {
    return $this->address("\n", $culture);
  }

  public function addressOneLiner($culture = null) {
    return $this->address(', ', $culture);
  } 
  
  public function getI18N() {
    return sfContext::getInstance()->getI18N();
  }
}