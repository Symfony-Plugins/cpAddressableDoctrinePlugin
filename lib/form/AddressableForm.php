<?php
abstract class AddressableForm extends BaseFormDoctrine {
  
  public function setup() {
    $this->setWidgets(array(
      'address1'    => new sfWidgetFormInputText(),
      'address2'   => new sfWidgetFormInputText(),
      'city'       => new sfWidgetFormInputText(),
      'zip'        => new sfWidgetFormInputText(),
      'state'      => new sfWidgetFormInputText(),
      'country_id' => new sfWidgetFormI18nChoiceCountry()
    ));

    $this->setValidators(array(
      'address1'    => new sfValidatorString(array('required' => true, 'max_length' => 36)),
      'address2'   => new sfValidatorString(array('required' => false, 'max_length' => 36)),
      'city'       => new sfValidatorString(array('required' => true, 'max_length' => 36)),
      'zip'        => new sfValidatorString(array('required' => false, 'max_length' => 20)),
      'state'      => new sfValidatorString(array('required' => false, 'max_length' => 10)),
      'country_id' => new sfValidatorI18nChoiceCountry(array('required' => true))
    ));

    $this->widgetSchema->setNameFormat('address[%s]');
    $this->setupInheritance();

    parent::setup();
    
    $this->setTranslationCatalogue('address');
  }
  
  protected function updateDefaultsFromObject() {
    $address = $this->getObject();
    $object_defaults = array(
      'address1' => $address->getAddress1(),
      'address2' => $address->getAddress2(),
      'city' => $address->getCity(),
      'zip' => $address->getZip(),
      'state' => $address->getState(),
      'country_id' => $address->getCountryId()
    );
    
    $defaults = $this->getDefaults();

    // update defaults for the main object
    if ($this->isNew()) {
      $defaults = $defaults + $object_defaults;
    }
    else {
      $defaults = $object_defaults + $defaults;
    } 

    foreach ($this->embeddedForms as $name => $form) {
      if ($form instanceof sfFormDoctrine) {
        $form->updateDefaultsFromObject();
        $defaults[$name] = $form->getDefaults();
      }
    }
    
    $this->setDefaults($defaults);
  }
  
  protected function doUpdateObject($values) {
    $this->getObject()->fromArray($values);
    $this->getObject()->setAddress1($values['address1']);
    $this->getObject()->setAddress2($values['address2']);
    $this->getObject()->setCity($values['city']);
    $this->getObject()->setZip($values['zip']);
    $this->getObject()->setState($values['state']);
    $this->getObject()->setCountryId($values['country_id']);
  }
  
}