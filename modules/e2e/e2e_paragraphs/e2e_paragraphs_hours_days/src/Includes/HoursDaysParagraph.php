<?php

namespace Drupal\e2e_paragraphs_hours_days\Includes;

class HoursDaysParagraph {
  
  private $daysoff;
  private $entity;
  private $exceptions;
  private $holidays;
  private $period;
  private $week;
  
  private $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');
  
  //constructor
  public function  __construct($entity){
    $this->entity =  $entity;
  }
  
  //get open, closed or empty status
  public function getStatus($datetime, $checkhours = TRUE){
    
    $timestamp = strtotime($datetime);
    
    $period = $this->getPeriod();
    
    //no open or closed outside periods
    if(!empty($period['value']) && !empty($period['end_value'])){
      if(!$this->isInDateRange($timestamp, array($period)))
          return '';
    }
    
    //check regular week days
    if($this->isInHourRange($timestamp, $this->getWeek(), $checkhours))
      return 'open';
    
      
    return 'closed';
  }
  
  //get week as a field for hours days paragraph
  public function getWeekField(){
    
      $weekfield = array();
    
      foreach($this->getWeek() as $date => $hourgroups){
        
          $weekfield[$date] = array();

          foreach($hourgroups as $index => $hourgroup){
            
            if(is_array($hourgroup)){
            
              foreach($hourgroup as $key => $parameter){
                $weekfield[$date][$index]['#' . $key] = $parameter;
              }

              $weekfield[$date][$index]['#theme'] =  'field_hours';
              
            }

          }
      }
    
     return $weekfield;
  }
  
  public function getAllDaysOff(){
    
    $alldaysoff = array();
    
    foreach($this->getDaysoff() as $dayoff){
      $date = \Drupal::service('date.formatter')->format(strtotime($dayoff['value']),'custom','d F Y');
      
      if($dayoff['value'] != $dayoff['end_value'])
        $date .= ' - ' . \Drupal::service('date.formatter')->format(strtotime($dayoff['end_value']),'custom','d F Y');
      
      $alldaysoff[] = $date;
    }
    
    return $alldaysoff;
  }
  
  //check if the timestamp is between two days
  private function isInDateRange($timestamp, $daterange){
    
    foreach($daterange as $date){
      
      $tsStart = strtotime($date['value'] . ' 00:00:00');
      $tsEnd = strtotime($date['end_value'] . ' 23:59:59');
      
      if($timestamp >= $tsStart && $timestamp <= $tsEnd)
        return TRUE;
    }
    
    return FALSE;
    
  }
  
  //check if the timestamp is within hour range on a certain date
  private function isInHourRange($timestamp, $hoursrange, $checkhours = TRUE){
    
    $date = \Drupal::service('date.formatter')->format($timestamp, 'custom', 'Y-m-d');
    
    if(isset($hoursrange[$date])){
      
      $hourgroups = $hoursrange[$date];
      
      if(!empty($hourgroups['closed']))
        return FALSE;
      
      if(!$checkhours)
        return TRUE;
      
      foreach($hourgroups as $hourgroup){
        
        if(is_array($hourgroup)){
        
          $tsFrom = strtotime($date . ' ' . $hourgroup['time_from']);
          $tsUntil = strtotime($date . ' ' . $hourgroup['time_until']);

          if($timestamp >= $tsFrom && $timestamp <= $tsUntil)
            return TRUE;
        }
      }
      
    }
    
    return FALSE;
    
  }
  
  //return days off dateranges
  private function getDaysoff(){
    
    if(!$this->daysoff)
      $this->setDaysoff();
    
    return $this->daysoff;
  }
  
  //get exception dates with hours
  private function getExceptions(){
    
    if(!$this->exceptions)
      $this->setExceptions();
    
    return $this->exceptions;
    
  }
  
  //return holiday date ranges
  private function getHolidays(){
    
    if(!$this->holidays)
      $this->setHolidays();
    
    return $this->holidays;
    
  }
  
  //get period start and end date
  public function getPeriod(){
    
    if(!$this->period)
      $this->setPeriod();
    
    return $this->period;
  }
  
  //get week days with hours
  private function getWeek(){
   
    if(!$this->week)
      $this->setWeek();
    
    return $this->week;
  }
  
   //get days off
  private function setDaysoff(){
    
    $daysoff = $this->getHolidays();
    
    if(!empty($this->entity->field_days->getValue()))
      $daysoff = array_merge($daysoff, $this->entity->field_days->getValue());
    
    usort($daysoff, function($a, $b){
      return strnatcmp($a['value'], $b['value']);
    });
    
    $this->daysoff = $daysoff;
    
  }
  
  //set exception dates with hours
  private function setExceptions(){
    
    $exceptions = array();
    
    foreach($this->entity->field_hours_exceptions as $exception){
      if(isset($exception->entity)){
        
        $exceptionday = $exception->entity->field_exception_day->getValue();
        
        if(!empty($exceptionday[0]['value'])){
          
          if(!empty($exception->entity->field_hours->getValue())){
            $exceptions[$exceptionday[0]['value']] = $exception->entity->field_hours->getValue();
          }
        }
      }
    }
    
    $this->exceptions = $exceptions;
    
  }
  
  //set holiday date ranges based on related holiday content
  private function setHolidays(){
    
    $holidays = array();
    
    foreach($this->entity->field_holidays_reference as $holiday){
      
      if(!empty($holiday->entity->field_days->getValue()))
        $holidays += $holiday->entity->field_days->getValue();
    }
    
    $this->holidays = $holidays;
  }
  
  //set period start and end date
  private function setPeriod(){
    
    $period = array('value' => '', 'end_value' => '');
    
    if(!empty($this->entity->field_period->getValue())){
      $periodfield = $this->entity->field_period->getValue();
      
      if(!empty($periodfield[0]['value'])){
        
        $timestamp = time();
        
        $period = $periodfield[0];
        
        if(empty($periodfield[0]['end_value']))
          $period['end_value'] = $period['value'];
        
        if($this->isInDateRange($timestamp, array($period)))
          $period['class'] = 'active-period';
        else
          $period['class'] = 'inactive-period';
        
        $period['date'] = 
            \Drupal::service('date.formatter')->format(strtotime($period['value']), 'custom', 'd/m/Y') . ' - ' .
            \Drupal::service('date.formatter')->format(strtotime($period['end_value']), 'custom', 'd/m/Y');
      }
    }
    
    $this->period = $period;
  }
  
  //set week days and hours based on current day
  private function setWeek(){
    
    $timestamp = time();
    $current_daynumber = \Drupal::service('date.formatter')->format($timestamp, 'custom', 'N');
    $week = array();
    $exceptions = $this->getExceptions();
    $daysoff = $this->getDaysoff();
    
    foreach($this->weekdays as $daynumber => $dayname){
      
      $fieldname = 'field_hours_' . $dayname;
      
      if(!empty($this->entity->$fieldname->getValue())){
        
        if($current_daynumber > $daynumber)
          $tsWeekday = $timestamp + ((7 - $current_daynumber + $daynumber) * 86400);
        elseif($current_daynumber < $daynumber)
          $tsWeekday = $timestamp + (($daynumber - $current_daynumber) * 86400);
        else
          $tsWeekday = $timestamp;
        
        $weekdate = \Drupal::service('date.formatter')->format($tsWeekday, 'custom', 'Y-m-d');
        
        if(isset($exceptions[$weekdate]))
          $week[$weekdate] = $exceptions[$weekdate];
        else  
          $week[$weekdate] = $this->entity->$fieldname->getValue();
        
        if($this->isInDateRange($tsWeekday, $daysoff)){
          $week[$weekdate][count($week[$weekdate]) - 1]['time_note'] = 'Exceptionally closed';
          $week[$weekdate]['closed'] = TRUE;
        }
        
        $this->entity->$fieldname->setValue($week[$weekdate]);
        
      }
    }
    
    $this->week = $week;
  }
}

