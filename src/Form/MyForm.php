<?php

namespace Drupal\BasicForm\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/*
  My form class
 */
class MyForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'BasicForm';
  }

  /**
   * {@inheritdoc}
   */

   public function buildForm(array $form, FormStateInterface $form_state) {

     $form['name'] = [
       '#type' => 'textfield',
       '#title' => $this->t('Name'),
       '#required' => TRUE,
     ];

     $form['age'] = [
       '#type' => 'number',
       '#title' => $this->t('Age'),
       '#required' => TRUE,
     ];

     $form['gender'] = [
       '#type' => 'select',
       '#title' => $this
         ->t('Gender'),
       '#options' => [
         'Prefer not to say' => $this
           ->t('Prefer not to say'),
         'Male' => $this
           ->t('Male'),
         'Female' => $this
           ->t('Female'),
       ],
     ];


     $form['bday'] = [
         '#type' => 'date',
         '#title' => $this->t('Date of birth'),
         '#required' => TRUE,
       ];



     $form['submit'] = [
       '#type' => 'submit',
       '#value' => $this->t('Send'),
     ];

     return $form;
   }
   /**
   * {@inheritdoc}
   */
   public function validateForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $age = $form_state->getValue('age');
    $gender = $form_state->getValue('gender');
    $bday = $form_state->getValue('bday');

    //VALIDATION


    //NAME CONTAINS ONLY LETTERS
    $tab = [' ', "'", '-'];
    if(!ctype_alpha(str_replace($tab, '', $name))){
      $form_state->setErrorByName('name', $this->t('Your name doesn\'t contain only letters'));
    }
    //NAME LENGTH
    if (strlen($name) == 0) {
      $form_state->setErrorByName('name', $this->t('Your name is too short'));
    }
    //AGE <0
    if ($age < 0) {
      $form_state->setErrorByName('age', $this->t('Your age is too low'));
    }
    //DATE CALCULATE
     $today = date("Y-m-d");
     $diff = date_diff(date_create($bday), date_create($today));
     $realage = $diff->format('%y');
     if($realage!=$age){
         $form_state->setErrorByName('age', $this->t('Your age doesn\'t match with calculated age'));
     }
     //DATE BIGGER THAN ACTUALDATE
     if( strtotime($bday) > strtotime('now')) {
         $form_state->setErrorByName('age', $this->t('Your date is greater than the current one'));
     }


   }

   /**
    * {@inheritdoc}
    */
   public function submitForm(array &$form, FormStateInterface $form_state) {
     $messenger = \Drupal::messenger();
     $name = $form_state->getValue('name');
     $age = $form_state->getValue('age');
     $gender = $form_state->getValue('gender');
     $bday = $form_state->getValue('bday');
     if($gender=='Prefer not to say'){
       $gensen = '';//Gender sentence
     } else {
       $gensen = ' You are ' . $gender . '.';
     }

     $messenger->addMessage($this->t('Your name is @name. Your age is @age.@gensen Your date of birth is @bday.', ['@name' => $name, '@age' => $age, '@gensen' => $gensen, '@bday' => $bday]));

   }

   }
