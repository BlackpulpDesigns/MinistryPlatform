This is a new library for interacting with MinistryPlatform's SOAP XML API. Several examples for the objects are listed below, and you can also view the /docs/ folder for a complete list of methods and class properties.

=Installation=

==Composer==

Require the library from the command line within your project.

<code lang="php">composer require "BlackpulpDesigns/MinistryPlatform"</code>

==One Time Setup==
 === Laravel 5.x === 
Open your project's .env file and add the following items along with their values.
<code lang="php">
MP_DOMAIN_GUID={{domain guid}}
MP_WSDL=https://elcid.blackpulp.com/ministryplatformapi/api.svc?WSDL
MP_API_PASSWORD={{api password||
MP_SERVER_NAME=elcid.blackpulp.com
</code>

Next, open /config/app.php and paste the following line at the bottom of your service providers array.

<code lang="php">'Blackpulp\MinistryPlatform\MinistryPlatformServiceProvider',</code>

Finally, publish the config file via the following artisan command.

<code lang="php">php artisan vendor:publish</code>

 === Lumen 5.x === 

Coming soon

=Usage=

<code lang="php">use Blackpulp\MinistryPlatform\MinistryPlatform;</code>

==Instantiating==

The MinistryPlatform construct accepts a user ID as an optional parameter. Obviously there are times where this won't be available, but you should pass the User ID whenever possible. It will be used in API calls that allow (or require) the value to be submitted. Note that the User ID is set automatically when the authenticate() method is used so the MP instance can continue to be used after authenticating where needed.

<code lang="php">
$user_id = 1;
$mp = new MinistryPlatform($user_id);
</code>

==Authenticating==
Simply pass the username and un-hashed password to the authenticate() method.

<code lang="php">
$mp = new MinistryPlatform();

$username = "Ken";
$password = "Password";
$user = $mp->authenticate($username, $password);
</code>

'''You should store the returned user object in your cache/session to access additional details related to the user in future HTTP Requests'''.

Additional methods available from the authenticated user's object:

 === Security Roles === 
Fetch the Security Roles for the authenticated user
<code lang="php">$security_roles = $user->getRoles();</code>

 === User Info === 
Retrieve various data from the API's "GetUserInfo()" method. $info is returned as a StoredProcedureResult object and can be interacted with in the same way as Stored Procedures.
<code lang="php">$info = $user->getInfo();</code>

Interact with the table data returned to the $info object.
<code lang="php">
$info->getTables();
$user_info = $info->getTable(0);
$contact_info = $info->getTable(1);
$prefixes = $info->getTable(2);
$suffixes = $info->getTable(3);
$genders = $info->getTable(4);
$marital_statuses = $info->getTable(5);
</code>

==Stored Procedures==
Pass the Stored Procedure name and an array of parameters to the storedProcedure method.

 === Execute Stored Procedure === 
<code lang="php">
$mp = new MinistryPlatform($_SESSION['audit_log_user_id']);

$sp = "api_blackpulp_FindMatchingContact";

$params = [
      "FirstName" => "Ken",
      "LastName" => "Mulford",
      "EmailAddress" => "ken@blackpulp.com",
      "Phone" => "863-594-4216",
      "DOB" => "11/17/1982"
    ];

$result = $mp->storedProcedure($sp, $params);
</code>

Once you have the StoredProcedureResult object ($result in the above example), you can examine it a bit further.

 === Retrieve Stored Procedure Results === 
Return the Raw XML data (so you can manually access $raw->NewDataSet->Table... though you really would never want to do that)
<code lang="php">$result->getRaw();</code>

Access array representations of all tables returned in the dataset (so you never have to deal with iterating over $raw->NewDataSet->Table ever again!
<code lang="php">$result->getTables();</code>

Access the array representation of a specific table in the dataset
<code lang="php">$result->getTable(0);</code>

==Adding/Updating Records==

Instantiating a new Record object requires the table name, the primary key's field name, and an array of field=>value pairs.

 === Ways to Instantiate a Record === 

1. Via MinistryPlatform::makeRecord()
<code lang="php">
$mp = new MinistryPlatform();
$table = "Contacts";
$primary_key = "Contact_ID";
$fields = [
  "Contact_ID"=>606805,
  "Mobile_Phone"=>"863-594-4216"
];

$record = $mp->makeRecord($table, $primary_key, $fields);
</code>

2. Via MinistryPlatform::makeTable()
<code lang="php">
$mp = new MinistryPlatform();
$table = "Contacts";
$primary_key = "Contact_ID";
$fields = [
  "Contact_ID"=>606805,
  "Mobile_Phone"=>"863-594-4216"
];

$record = $mp->makeTable($table, $primary_key)->makeRecord($fields);
</code>

Note that both methods result in the same resulting Record object.

 === Saving Records === 

Saving is as simple as calling the save method. A new record will be created if the Record's $primary_key value is not an array key in the $record->fields array. Otherwise, the Primary Key will be provided and the record will be updated instead.
<code lang="php">$record->save()</code>

'''Please Note: '''When a record is initially created, the Primary Key value is automatically appended to the list of fields for that record. Doing this ensures that if $record->save() is called a second time you will not accidentally create another new record. If you must create a second record, create a new instance of the Record object.

 === Examples === 

'''existing''' Contact record
<code lang="php">
  $contact = $this->mp->makeRecord( 
      "Contacts",
      "Contact_ID",
      [
        'Contact_ID' => 606805,
        'First_Name' => 'Ken',
        'Last_Name' => 'Mulford',
        'Display_Name' => 'Mulford, Ken',
        'Contact_Status_ID' => 1,
        'Household_Position_ID' => 1,
        'Company' => 0,
        'Bulk_Email_Opt_Out' => 0,
        'College_Graduation_Year' => 2001
      ]
    );

    // Will call UpdateRecord() because Contact_ID is present in the fields array
    $contact->save();
</code>

'''new''' Event record
<code lang="php">
    $event = $this->mp->makeRecord( 
      "Events",
      "Event_ID",
      [
        'Event_Title' => "Blackpulp Family Reunion",
        'Event_Type_ID' => 8,
        'Congregation_ID' => 1,
        'Meeting_Instructions' => "Bring your own Legos",
        "Description" => "The most funnest thing full of fun ever!!!",
        "Program_ID" => 2064,
        "Primary_Contact" => 606805,
        "Minutes_For_Setup" => 0,
        "Event_Start_Date" => Carbon::now()->addMonth()->toDateTimeString(),
        "Event_End_Date" => Carbon::now()->addMonth()->addHours(2)->toDateTimeString(),
        "Minutes_For_Cleanup" => 0,
        "Cancelled" => 0,
        "Visibility_Level_ID" => 4,
        "Featured_On_Calendar" => 1,
        "Registration_Start" => Carbon::now()->toDateTimeString(),
        "Registration_End" => Carbon::now()->addMonth()->toDateTimeString(),
        "Registration_Active" => 1,
      ]
    );
    
    // Will perform AddRecord() because Event_ID is not present in the fields array
    $event->save();
</code>

==Attaching Files==

 === Instantiate a File === 
<code lang="php">
$mp = new MinistryPlatform();

$file_name = "Blackpulp Logo";
$abs_file_path = "/path/to/file/blackpulp.png";
$file_description = "This is the description";
$resize_pixels = 0;
$file = $mp->makeFile(
      $file_name, 
      $abs_file_path,
      $file_description,
      292,
      606805,
      true,
      $resize_pixels
);
</code>

 === Attaching the file === 
<code lang="php">$file->attach();</code>

 === Set File as Default Image === 
<code lang="php">$file->makeDefault();</code>

==Recurring Records==

Recurring records require an instance of the Record object (see above).

 === Instantiating === 

This will extend the addRecord() example where a new Event record was created.
<code lang="php">
$series = $event->makeRecurring();
</code>

At this point, $series represents a RecurringRecord object. Before we can save it though, we have quite a few values to set. Every set() method is chainable.

<code lang="php">
    $series
      ->setCsvSubTabIds("281")
      ->setPattern(2)
      ->setFrequency(1)
      ->setSubTabSourceRecordId( (int)$event->getId() )
      ->setStartBy( $event_fields['Event_Start_Date'] )
      ->setEndBy( Carbon::now()->addYears(2)->toDateTimeString() )
      ->setEndAfter(0)
      ->setSpecificDay(0)
      ->setSpecificMonth(0)
      ->setOrderDay(0)
      ->setSunday(0)
      ->setMonday(0)
      ->setTuesday(1)
      ->setWednesday(0)
      ->setThursday(0)
      ->setFriday(0)
      ->setSaturday(0);
</code>

 === Creating the Recurrence in MP === 
Once the RecurringRecord object has been adequately set, you can save the series.
<code lang="php">$series->create();</code>

 === Misc === 

Since the Record object is only a property, if you had two separate records that both needed the same series you can update the Record and generate the new series pretty easily.

<code lang="php">
    $mp = new MinistryPlatform();
    $event = new Record($mp, "Events", "Event_ID", ["Event_ID" => 1000, Event_Title="My First Event"]);    
    $event2 = new Record($mp, "Events", "Event_ID", ["Event_ID" => 1001, Event_Title="My Second Event"]);

    $series = $event->makeRecurring()
      ->setCsvSubTabIds("281")
      ->setPattern(2)
      ->setFrequency(1)
      ->setSubTabSourceRecordId( (int)$event->getId() )
      ->setStartBy( $event_fields['Event_Start_Date'] )
      ->setEndBy( Carbon::now()->addYears(2)->toDateTimeString() )
      ->setEndAfter(0)
      ->setSpecificDay(0)
      ->setSpecificMonth(0)
      ->setOrderDay(0)
      ->setSunday(0)
      ->setMonday(0)
      ->setTuesday(1)
      ->setWednesday(0)
      ->setThursday(0)
      ->setFriday(0)
      ->setSaturday(0);

    // Creates the recurrence for $event
    $series->create();
    
    // Updates the record and creates the recurrence for $event2
    $series->setRecord($event2);
    $series->create();
</code>
