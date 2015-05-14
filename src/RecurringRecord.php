<?php namespace Blackpulp\MinistryPlatform;

use Carbon\Carbon;

/**
 * MinistryPlatform Records
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles recurring record interactions.
 */
class RecurringRecord
{   
  
  /**
   * The base Record object
   * @var Blackpulp\MinistryPlatform\Record
   */
  protected $record;

  /**
   * A comma separated list of sub_page_ids that should be copied
   * @var string
   */
  protected $csv_sub_tab_ids = "";

  /**
   * This is the primary key value for the record which serves as the model containing sub tab data 
   * to be copied into the recurrence * series members.
   * @var int
   */
  protected $sub_tab_source_record_id;

  /**
   * The interval of the recurrence (Daily = 1, Weekly = 2, Monthly = 3, Yearly = 4)
   * @var int
   */
  protected $pattern;
  /**
   * The integer value of the frequency for the specified pattern. A value
   * of 0 for the *daily* pattern indicates weekdays only.
   * @var int
   */
  protected $frequency;

  /**
   * The date on which to begin the recurrence.
   * @var datetime
   */
  protected $start_by;
  
  /**
   * The date on which to terminate the recurrence.
   * @var datetime
   */
  protected $end_by;
  
  /**
   * Explicit number of occurrences after which to terminate the series.
   * @var int
   */
  protected $end_after;

  /**
   * The numeric value of a specific day being targeted.
   * @var int
   *
   * @example Monthly: For value X, Day X of every <month> (the 15th of each month)
   * @example Yearly: For value X, Every <specific month> X (Every May 15)
   */
  protected $specific_day;

  /**
   * The nth week of a month/year. 1-5 are accepted values, use 5 for "last".
   * @var int
   */
  protected $order_day;

  /**
   * The month of hte year the recurrence will occur. Acceptable numbers are 1-12.
   * @var int
   */
  protected $specific_month;

  /**
   * Recurring series will occur on the specific day of the week.
   * @var boolean
   */
  protected $sunday = false;

  /**
   * Recurring series will occur on the specific day of the week.
   * @var boolean
   */
  protected $monday = false;

  /**
   * Recurring series will occur on the specific day of the week.
   * @var boolean
   */
  protected $tuesday = false;

  /**
   * Recurring series will occur on the specific day of the week.
   * @var boolean
   */
  protected $wednesday = false;

  /**
   * Recurring series will occur on the specific day of the week.
   * @var boolean
   */
  protected $thursday = false;

  /**
   * Recurring series will occur on the specific day of the week.
   * @var boolean
   */
  protected $friday = false;

  /**
   * Recurring series will occur on the specific day of the week.
   * @var boolean
   */
  protected $saturday = false;

  /**
   * The Series ID returned from the API call
   * @var int
   */
  protected $series_id;

  /**
   * The message returned from the most recent API call.
   * @var string
   */
  protected $message;

  /**
   * The first date in the recurring series.
   * @var datetime
   */

  protected $first_date;

  /**
   * All details for the current series
   * @var array
   */
  protected $series_details;

  /**
   * Value returned from MinistryPlatform that describes the created series.
   * @var string
   */
  protected $series_description;


  /**
   * Instantiate the object.
   * 
   * @param Record $record
   */
  public function __construct(Record $record) {
    
    $this->record = $record;

  }

  /**
   * Create the Recurring Records in MinistryPlatform.
   * 
   * @return RecurringRecord
   */
  public function create() {

    $return = $this->record->getMpInstance()->createRecurringSeries($this);

    $this->series_id = $return[0];
    $this->message = $return[2];

    return $this;
  }

  /**
   * Retrieve the first date in the current series.
   * 
   * @return string
   */
  public function getFirstDate() {

    $this->first_date = $this->record->getMpInstance()->getFirstDateInSeries($this);

    return $this;

  }

  /**
   * Return the description of the current series.
   * 
   * @return string
   */
  public function getSeriesDescription() {

    if( !isset($this->series_description) || $this->series_description == "" ) {
      $this->setSeriesDetails();
    }

    return $this->series_description;

  }

  /**
   * Return an array of dates representing each instance of the current series.
   * 
   * @return array The array contains Carbon objects
   */
  public function getSeriesDetails() {

    if( !isset($this->series_details) || count($this->series_details) == 0 ) {
      $this->setSeriesDetails();
    }

    return $this->series_details;

  }

  /**
   * Set the Series Details.
   *
   * @return RecurringRecord
   */
  public function setSeriesDetails() {

    $details = $this->record->getMpInstance()->getRecurringRecords($this);
    $rows = [];
    foreach($details->getTable(0) as $item) {

      $rows[] = new Carbon($item);

    }
    $this->series_details = $rows;

    $description = $details->getTable(1);
    $this->series_description = $description['PatternName'];

    return $this;

  }


  /**
   * Gets the current Record object
   * @return Blackpulp\MinistryPlatform\Record
   */
  public function getRecord() {

    return $this->record;

  }

  /**
   * Set the current Record property.
   * @param Record $record
   */
  public function setRecord(Record $record) {

    $this->record = $record;

    return $this;

  }

  /**
   * Gets the A comma separated list of sub_page_ids that should be copied.
   *
   * @return string
   */
  public function getCsvSubTabIds()
  {
    return $this->csv_sub_tab_ids;
  }

  /**
   * Sets the A comma separated list of sub_page_ids that should be copied.
   *
   * @param string $csv_sub_tab_ids the csv sub tab ids
   *
   * @return self
   */
  public function setCsvSubTabIds($csv_sub_tab_ids)
  {
    $this->csv_sub_tab_ids = $csv_sub_tab_ids;

    return $this;
  }

  /**
   * Gets the This is the primary key value for the record which serves as the model containing sub tab data
   * to be copied into the recurrence * series members.
   *
   * @return int
   */
  public function getSubTabSourceRecordId()
  {
    return $this->sub_tab_source_record_id;
  }

  /**
   * Sets the This is the primary key value for the record which serves as the model containing sub tab data
   * to be copied into the recurrence * series members.
   *
   * @param int $sub_tab_source_record_id the sub tab source record id
   *
   * @return self
   */
  public function setSubTabSourceRecordId($sub_tab_source_record_id)
  {
    $this->sub_tab_source_record_id = $sub_tab_source_record_id;

    return $this;
  }

  /**
   * Gets the The interval of the recurrence (Daily = 1, Weekly = 2, Monthly = 3, Yearly = 4).
   *
   * @return [type]
   */
  public function getPattern()
  {
    return $this->pattern;
  }

  /**
   * Sets the The interval of the recurrence (Daily = 1, Weekly = 2, Monthly = 3, Yearly = 4).
   *
   * @param [type] $pattern the pattern
   *
   * @return self
   */
  public function setPattern($pattern)
  {
    $this->pattern = $pattern;

    return $this;
  }

  /**
   * Gets the The integer value of the frequency for the specified pattern. A value
   * of 0 for the *daily* pattern indicates weekdays only.
   *
   * @return int
   */
  public function getFrequency()
  {
    return $this->frequency;
  }

  /**
   * Sets the The integer value of the frequency for the specified pattern. A value
   * of 0 for the *daily* pattern indicates weekdays only.
   *
   * @param int $frequency the frequency
   *
   * @return self
   */
  public function setFrequency($frequency)
  {
    $this->frequency = $frequency;

    return $this;
  }

  /**
   * Gets the The date on which to begin the recurrence.
   *
   * @return datetime
   */
  public function getStartBy()
  {
    return $this->start_by;
  }

  /**
   * Sets the The date on which to begin the recurrence.
   *
   * @param datetime $start_by the start by
   *
   * @return self
   */
  public function setStartBy($start_by)
  {
    $this->start_by = $start_by;

    return $this;
  }

  /**
   * Gets the The date on which to terminate the recurrence.
   *
   * @return datetime
   */
  public function getEndBy()
  {
    return $this->end_by;
  }

  /**
   * Sets the The date on which to terminate the recurrence.
   *
   * @param datetime $end_by the end by
   *
   * @return self
   */
  public function setEndBy($end_by)
  {
    $this->end_by = $end_by;

    return $this;
  }

  /**
   * Gets the Explicit number of occurrences after which to terminate the series.
   *
   * @return int
   */
  public function getEndAfter()
  {
    return $this->end_after;
  }

  /**
   * Sets the Explicit number of occurrences after which to terminate the series.
   *
   * @param int $end_after the end after
   *
   * @return self
   */
  public function setEndAfter($end_after)
  {
    $this->end_after = $end_after;

    return $this;
  }

  /**
   * Gets the The numeric value of a specific day being targeted.
   *
   * @return int
   */
  public function getSpecificDay()
  {
    return $this->specific_day;
  }

  /**
   * Sets the The numeric value of a specific day being targeted.
   *
   * @param int $specific_day the specific day
   *
   * @return self
   */
  public function setSpecificDay($specific_day)
  {
    $this->specific_day = $specific_day;

    return $this;
  }

  /**
   * Gets the The nth week of a month/year. 1-5 are accepted values, use 5 for "last".
   *
   * @return int
   */
  public function getOrderDay()
  {
    return $this->order_day;
  }

  /**
   * Sets the The nth week of a month/year. 1-5 are accepted values, use 5 for "last".
   *
   * @param int $order_day the order day
   *
   * @return self
   */
  public function setOrderDay($order_day)
  {
    $this->order_day = $order_day;

    return $this;
  }

  /**
   * Gets the The month of hte year the recurrence will occur. Acceptable numbers are 1-12.
   *
   * @return int
   */
  public function getSpecificMonth()
  {
    return $this->specific_month;
  }

  /**
   * Sets the The month of hte year the recurrence will occur. Acceptable numbers are 1-12.
   *
   * @param int $specific_month the specific month
   *
   * @return self
   */
  public function setSpecificMonth($specific_month)
  {
    $this->specific_month = $specific_month;

    return $this;
  }

  /**
   * Gets the Recurring series will occur on the specific day of the week.
   *
   * @return boolean
   */
  public function getSunday()
  {
    return $this->sunday;
  }

  /**
   * Sets the Recurring series will occur on the specific day of the week.
   *
   * @param boolean $sunday the sunday
   *
   * @return self
   */
  public function setSunday($sunday)
  {
    $this->sunday = $sunday;

    return $this;
  }

  /**
   * Gets the value of monday.
   *
   * @return mixed
   */
  public function getMonday()
  {
    return $this->monday;
  }

  /**
   * Sets the value of monday.
   *
   * @param mixed $monday the monday
   *
   * @return self
   */
  public function setMonday($monday)
  {
    $this->monday = $monday;

    return $this;
  }

  /**
   * Gets the value of tuesday.
   *
   * @return mixed
   */
  public function getTuesday()
  {
    return $this->tuesday;
  }

  /**
   * Sets the value of tuesday.
   *
   * @param mixed $tuesday the tuesday
   *
   * @return self
   */
  public function setTuesday($tuesday)
  {
    $this->tuesday = $tuesday;

    return $this;
  }

  /**
   * Gets the value of wednesday.
   *
   * @return mixed
   */
  public function getWednesday()
  {
    return $this->wednesday;
  }

  /**
   * Sets the value of wednesday.
   *
   * @param mixed $wednesday the wednesday
   *
   * @return self
   */
  public function setWednesday($wednesday)
  {
    $this->wednesday = $wednesday;

    return $this;
  }

  /**
   * Gets the value of thursday.
   *
   * @return mixed
   */
  public function getThursday()
  {
    return $this->thursday;
  }

  /**
   * Sets the value of thursday.
   *
   * @param mixed $thursday the thursday
   *
   * @return self
   */
  public function setThursday($thursday)
  {
    $this->thursday = $thursday;

    return $this;
  }

  /**
   * Gets the value of friday.
   *
   * @return mixed
   */
  public function getFriday()
  {
    return $this->friday;
  }

  /**
   * Sets the value of friday.
   *
   * @param mixed $friday the friday
   *
   * @return self
   */
  public function setFriday($friday)
  {
    $this->friday = $friday;

    return $this;
  }

  /**
   * Gets the value of saturday.
   *
   * @return mixed
   */
  public function getSaturday()
  {
    return $this->saturday;
  }

  /**
   * Sets the value of saturday.
   *
   * @param mixed $saturday the saturday
   *
   * @return self
   */
  public function setSaturday($saturday)
  {
    $this->saturday = $saturday;

    return $this;
  }
}
