# Sendios API and PHP SDK
Sendios is email marketing company https://sendios.io <br>
Below you can find the technical documentation of integration with our platform.<br>
You can use this PHP library, if PHP is your backend language, or do HTTP requests (cURL) with any other.

- [Providing account info and credentials](https://github.com/sendios/php-sdk#providing-account-info-and-credentials)
- [Sending email](https://github.com/sendios/php-sdk#sending-email-via-php-sdk)
- [Email validation](https://github.com/sendios/php-sdk#check-email)
- [User info](https://github.com/sendios/php-sdk#user-info)
- [User's custom fields](https://github.com/sendios/php-sdk#create-and-update-user-data)
- [Unsubscribe](https://github.com/sendios/php-sdk#unsubscribe)
- [Online on product](https://github.com/sendios/php-sdk#online)
- [Payments on product](https://github.com/sendios/php-sdk#payments)
- [Custom product events](https://github.com/sendios/php-sdk#product-events)


## Installing PHP SDK
```shell
composer require sendios/php-sdk
```

## Providing account info and credentials
```php
# PHP SDK
$clientId = 123;
$clientToken = 'a1s2d3f4g5h6j7k8l';
$sendios = new \Sendios\SendiosSdk($clientId, $clientToken);
```

```shell
# console cURL
curl -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 957081746b54977d51bef9fc74f4d4fd023bab13 is sha1 of clientToken (a1s2d3f4g5h6j7k8l)
```

Error with text "Authorization base64 data wrong or invalid" means that you provided wrong clientId or clientToken



## Sending email via PHP SDK
```php
// Required params for letter
$typeId = 1; // letter id (aka type_id)
$categoryId = $sendios->push->getCategorySystem(); // system or trigger
$projectId = 1; // in your admin panel
$email = 'test@example.com'; // for matching user

// User will be autocreated via any first letter

// Variables for letter
$data = [ // Data for letter
    'some' => 'hi',
    'letter' => 'John',
    'variables' => '!',
];

// User info, that will be saved [not required]
$user = [
    'name' => 'John',
    'age' => '22',
    'gender' => 'm',
    'language' => 'en',
    'country' => 'US',
    'platform_id' => $sendios->user->getPlatformDesktop(),
    'vip' => 0,
    'photo' => 'http://example.com/somephotourl.jpg',
    'channel_id' => 42,
    'subchannel_id' => 298,
    'client_user_id' => '123xyz'
];
// Your data, that will be sent with our webhooks
$meta = [
    'tracking_id' => 72348234,
];

// Sending
$response = $sendios->push->send($typeId, $categoryId, $projectId, $email, $user, $data, $meta);
// it will make POST to /push/system or /push/trigger with json http://pastebin.com/raw/Dy3VeZpB

var_dump($response);
// 
```

## Sending email via cURL
```shell
curl -X POST https://api.sendios.io/v1/push/system \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'

# or https://api.sendios.io/v1/push/trigger for trigger letters
# 957081746b54977d51bef9fc74f4d4fd023bab13 is sha1 of clientToken (a1s2d3f4g5h6j7k8l)
```

```json
{
    "type_id": 1,
    "category": 1,
    "client_id": 123,
    "project_id": 1,
    "data": {
        "user": {
            "email": "test@example.com",
            "name": "John",
            "age": 22
        },
        "some": "hi",
        "letter": "John",
        "variables": "!"
    },
    "meta": {
        "tracking_id": 72348234
    }
}
```


# Check email
Email validation
```php
$result = $sendios->email->check('Test@Example.com');
/* Returned array(
  'orig' => 'Test@Example.com',
  'valid' => false, // result
  'reason' => 'mx_record', // reason of result
  'email' => 'test@example.com', // fixed email
  'vendor' => 'Unknown', // vendor name like Gmail
  'domain' => 'example.com',
  'trusted' => false,
) */
```
```shell
curl -X POST https://api.sendios.io/v1/email/check \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d '{"email":"Test@Example.com"}'
```

# User
## User info
```php
$projectId = 1;
// Make GET to /user/project/PROJECT_ID/email/Test@Example.com
$user = $sendios->user->getByEmail('Test@Example.com', $projectId);
/* Returned array(
    "id":8424,
    "project_id":1,
    "email":"test@example.com",
    "name":"John",
    "gender":"m",
    "country":"UKR",
    "language":"en",
    ...
) */
```

```shell
curl -X GET https://api.sendios.io/v1/user/project/1/email/test@example.com \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```

## Providing user_id on Product
We call that Client's User ID
```php
$email = 'john@gmail.com';
$projectId = 1;
$clientUserId = 1;

$sendios->clientUser->create($email, $projectId, $clientUserId);
```

## Create and update user data
```php
$fields = [
    'name' => 'John Dou',
    'gender' => 'm', //m or f
    'age' => 21, //int
    'photo' => 'http://moheban-ahlebeit.com/images/Face-Wallpaper/Face-Wallpaper-26.jpg',//image url
    'ak' => 'FFZxYfCfGgNDvmZRqnELYqU7',//Auth key
    'vip' => 1, //int
    'language' => 'es', //ISO 639-1
    'country' => 'esp', //ISO 3166-1 alpha-3 or ISO 3166-1 alpha-2
    'platform_id' => $sendios->user->getPlatformDesktop(),
    'list_id' => 1,
    'status' => 0, //int
    'partner_id' => 1, //int

    // Your own custom fields may be here
    // allowed only int values
    'field1' => 542, //int
    'sessions_count' => 22, //int
    'session_last' => 1498137772, //unix timestamp
];
```
By email and project ID

```php
$result = $sendios->user->setUserFieldsByEmailAndProjectId('ercling@yandex.ru', 2, $fields);
// $result is a boolean status
```

```shell
curl -X PUT https://api.sendios.io/v1/userfields/project/1/emailhash/dGVzdEBleGFtcGxlLmNvbQ== \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'

# 'dGVzdEBleGFtcGxlLmNvbQ==' - base64_encode(test@example.com) 
```

```json
JSON_DATA
{
    "name": "John Dou",
    "gender": "m",
    "age": 21,
    "photo": "http://moheban-ahlebeit.com/images/Face-Wallpaper/Face-Wallpaper-26.jpg",
    "ak": "FFZxYfCfGgNDvmZRqnELYqU7",
    "vip": 1,
    "language": "es",
    "country": "esp",
    "platform_id": 1,
    "list_id": 1,
    "status": 0, 
    "partner_id": 1,
    "field1": 542,
    "sessions_count": 22,
    "session_last": 1498137772
}
```

By user

```php
$user = $sendios->user->getById(892396028);
$result = $sendios->user->setUserFieldsByUser($user, $fields);
// $result is a boolean status
```

```shell
curl -X PUT https://api.sendios.io/v1/userfields/user/5234 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'

# 5234 - user_id
```

```json
JSON_DATA
{
    "field1": 542,
    "field2": 22,
    "field3": 1498137772
}
```

## Get user custom fields

```php
$result = $sendios->user->getUserFieldsByEmailAndProjectId('ercling@yandex.ru', 1);
// or
$result = $sendios->user->getUserFieldsByUser($user);
/*
Returns [
    'user' => [
        'id' => 892396028,
        'project_id' => 1,
         ...
    ],
    'custom_fields' => [
        'sessions_count' => 22,
         ...
    ],
]
*/
```

```shell
curl -X GET https://api.sendios.io/v1/userfields/user/5234\
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 5234 - user_id     
```

```shell
curl -X GET https://api.sendios.io/v1/userfields/project/1/email/test@example.com \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```


# Unsubscribe
```php
$projectId = 1;
$user = $sendios->user->getByEmail('test@example.com', $projectId);
$unsub = $sendios->unsub->addBySettings($user);
// $user - array with $user[id] == 8424 (our user id)
// addBySettings reason == 9
```
```shell
curl -X POST https://api.sendios.io/v1/unsub/8424/source/9 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```

## Subscribe back
```php
$projectId = 1;
$user = $sendios->user->getByEmail('test@example.com', $projectId);
// Make DELETE to /unsub/USER_ID
$unsub = $sendios->unsub->subscribe($user);
```
```shell
curl -X DELETE https://api.sendios.io/v1/unsub/8424 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```
## Unsubscribe by admin

```php
$projectId = 123;
$result = $sendios->unsub->unsubByAdmin('test@example.com',$projectId);

/*
success result
array(1) {
  'unsub' => bool(true)
}
error result (already unsubscribed)
array(1) {
  'unsub' => bool(false)
}
*/
```

```shell
curl -X POST https://api.sendios.io/v1/unsub/admin/1/email/dGVzdEBleGFtcGxlLmNvbQ== \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 'dGVzdEBleGFtcGxlLmNvbQ==' - base64_encode(test@example.com) 
```

## Check is unsubscribed
By user:
```php
$projectId = 1;
$user = $sendios->user->getByEmail('test@example.com', $projectId);
if($user) {
$unsub = $sendios->unsub->isUnsubByUser($user); // Returns false(if not unsubscribed) or unsub data
}
```

By email and project:
```php
$projectId = 1;
$unsub = $sendios->unsub->isUnsubByEmailAndProjectId('test@example.com', $projectId); // Returns false(if not unsubscribed) or unsub data
```

```shell
curl -X GET https://api.sendios.io/v1/unsub/isunsub/5234 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 5234 - user_id    
```

## Get unsubscribe reason

```php
$projectId = 123;
$result = $sendios->unsub->getUnsubscribeReason('test@example.com',$projectId);

//user does not unsubscribed
array(1) {
  'result' => bool(false)
}

//reason for the unsubscription is unknown
array(1) {
  'result' => string(7) "Unknown"
}

//success result
array(1) {
  'result' => string(5) "admin"
}
```

```shell
curl -X GET https://api.sendios.io/v1/unsub/unsubreason/5234 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 5234 - user_id    
```

## Get unsubscribed list

```php
<?php

$result = $sendios->unsub->getByDate('2018-06-10');

//Response example
return [
    0 => [
        'email' => "jo23lu56@gmail.com",
        'project_id' => 9,
        'client_user_id' => NULL,
        'source_id' => 9,
        'created_at' => "2018-02-27 20:31:45"
    ],
    // ...
];
```
```shell
curl -X GET https://api.sendios.io/v1/unsub/list/1262307661 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# timestamp(2010-01-01) = 1262307661    
```

# Unsubscribe from types
### Get current unsubs
```php
$projectId = 1;
$user = $sendios->user->getByEmail('test@example.com', $projectId);
$list = $sendios->unsubTypes->getList($user);
//returns array {
//  [0] =>
//  array(3) {
//    'type_id' =>
//   int(3)
//    'unsubscribed' =>
//    bool(false)
//    'name' =>
//    string(11) "Popular now"
//  },
//  ...
//}
```

```shell
curl -X GET https://api.sendios.io/v1/unsubtypes/5234 \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13

# 5234 - user_id  
```

### Unsubscribe user from types 4 and 5
```php
$sendios->unsubTypes->addTypes($user, [4, 5]);
```
```shell
curl -X POST https://api.sendios.io/v1/unsubtypes/nodiff/[SENDIOS_USER_ID] \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d '{"type_ids": [4, 5]}'
```

### Subscribe user back to types 4 and 5
```php
$sendios->unsubTypes->removeTypes($user, [4, 5]);
```
```shell
curl -X DELETE https://api.sendios.io/v1/unsubtypes/nodiff/[SENDIOS_USER_ID] \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d '{"type_ids": [4, 5]}'
```

### Subscribe user back to all types
```php
$sendios->unsubTypes->removeAll($user); 
```
```shell
curl -X DELETE https://api.sendios.io/v1/unsubtypes/all/[SENDIOS_USER_ID] \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13
```

# Online
Online on product. Useful in loggics and personalization
```php
$sendios->user->setOnlineByUser($user, new \DateTime());
```
```shell
curl -X PUT https://api.sendios.io/v3/users/5234/online \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'

# 5234 - user_id 
```
```json
JSON_DATA
{
    "timestamp": "2010-01-01T08:15:30-01:00",
    "user_id": 5234
}
```
Update online by user email
```php
$sendios->user->setOnlineByEmailAndProjectId('ercling@gmail.com', 1, new \DateTime());
```
```shell
curl -X PUT https://api.sendios.io/v3/users/project/1/email/dGVzdEBleGFtcGxlLmNvbQ==/online \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'

# 'dGVzdEBleGFtcGxlLmNvbQ==' - base64_encode(test@example.com) 
```
```json
JSON_DATA
{
    "timestamp": "2010-01-01T08:15:30-01:00",
    "project_id": 1,
    "encoded_email": "dGVzdEBleGFtcGxlLmNvbQ==" 
}
```

# Payments
```php
$startDate = 1509617696; //Payment date or subscription start date 
$expireDate = 1609617696; //Subscription end date (optional, default false)
$paymentCount = 14; //Pay count (optional, default false)
$paymentType = 1; //Pay type (optional, default false)
$amount = 20; //Pay amount (optional, default false)
```
By email and project ID

```php
$result = $sendios->user->addPaymentByEmailAndProjectId('ercling@yandex.ru', 2, $startDate, $expireDate, $paymentCount, $paymentType, $amount);
// $result is a boolean status
```

By user

```php
$user = $sendios->user->getById(892396028);
$result = $sendios->user->addPaymentByUser($user, $startDate, $expireDate, $paymentCount, $paymentType, $amount);
// $result is a boolean status
```

```shell
curl -X POST https://api.sendios.io/v3/lastpayment \
    -u 123:957081746b54977d51bef9fc74f4d4fd023bab13 \
    -d 'JSON_DATA'
```

```json
JSON_DATA
{
    "user_id": 1, //User id in sendios
    "start_date": "1509617696", //Payment date or subscription start date 
    "expire_date": "1609617696", //Subscription end date (optional, default false)
    "total_count": "14", //Pay count (optional, default false)
    "payment_type": "1", //Pay type (optional, default false)
    "amount": "20" //Pay amount (optional, default false)
}
```

Attempt to send incorrect data
```php
$sendios = new \Sendios(3,'GH3ir1ZDMjRkNzg4MzgzE3MjU');
$fields = [
    'language' => 'ua',
    'gender' => 'male',
    'vip' => 'yes',
];
$result = $sendios->user->setUserFieldsByEmailAndProjectId('ercling@yandex.ru', 2, $fields);
if (!$result){
    var_dump($sendios->request->getLastResponse()->getData());
}

//array(3) {
//  'errorCode' =>
//  int(409)
//  'message' =>
//  string(16) "Validation error"
//  'errors' =>
//  array(3) {
//    'language' =>
//    string(45) "Field language is not valid language code: ua"
//    'gender' =>
//    string(41) "Field gender must be a part of list: m, f"
//    'vip' =>
//    string(44) "Field vip does not match the required format"
//  }
//}
```

## Goals on product

```php
$data = [
    [
        'email' => 'someone@example.com',
        'type' => 'some_type',
        'project_id' => 123,
        'mail_id' => '123123123',
    ],
    [
        'email' => 'someone1@example.com',
        'type' => 'some_type',
        'project_id' => 345,
        'mail_id' => '345345345',
    ]];

$res = $sendios->goal->createGoal($data);





```

Success response

```php
/*
array(1) {
  'goals_added' => int(2)
}
*/
```


Error response

```php
/*
array(3) {
  'goals_added' =>
  int(0)
  [0] =>
  array(4) {
    'error_messages' =>
    array(1) {
      [0] =>
      string(25) "Parameter type is invalid"
    }
    'errorCode' =>
    int(409)
    'message' =>
    string(16) "Validation error"
    'goal_data' =>
    string(39) "somemail@example.com;<h1>;123;123123123"
  }
  [1] =>
  array(4) {
    'error_messages' =>
    array(1) {
      [0] =>
      string(26) "Parameter email is invalid"
    }
    'errorCode' =>
    int(409)
    'message' =>
    string(16) "Validation error"
    'goal_data' =>
    string(46) "somem@ail1@example.com;some_type;345;345345345"
  }
}
*/
```

## Send goals without sdk

```php
POST https://api.sendios.io/v1/goals
params: {
            'type' : 'contact',
            'email' : 'andrey.reinwald@corp.flirchi.com', 
            'project_id' : 30,
            'mail_id' : 2739212714|null
        }
```

Request format

Name | Type | Description
-------|------|-------
`type`|`string`| **Required.** Goal type
`email`|`string`| **Required.** User email 
`project_id`|`int`| **Required.** Id of your project. You can find it at https://admin.sendios.io/account/projects 
`mail_id`|`int`| Mail id after which the user made a goal

# Product Events
Data format

Name | Type | Description
-------|------|-------
`project_id`|`int`| **Required.** User project id
`event_id`|`int`| **Required.** Event id 
`uid`|`bigint`| **Required.** uid of event from your product 
`receiver_id`|`int`| **Required.** Sendios user id whose event occurred
`sender_id`|`int`| Sendios user (id) associated with the receiver_id 
`sender_product_id`|`int`| Product user (id) associated with the receiver_id 
`date`|`string`| Event date. Example: '2018-10-24 19:40:22'

```php
        $projectId = 1;
        $eventId = 1;
        $uid = 1;
        $receiverId = 1;
        $senderId = 1;
        $senderProductId = 1;
        $date = '2018-10-24 19:40:22';

        $event = [
            'project_id' => $projectId,
            'event_id' => $eventId,
            'uid' => $uid,
            'receiver_id' => $receiverId,
            'sender_id' => $senderId,
            'sender_product_id' => $senderProductId,
            'date' => $date,
        ];
        
        $events[] = $event;
        
        $sendios->event->send($events);
```

# Other

## Get response (if $result === false)
```php
$response = $sendios->request->getLastResponse()->getData();

//array(3) {
//  'errorCode' =>
//  int(409)
//  'message' =>
//  string(16) "Validation error"
//  'errors' =>
//  array(1) {
//    'field_name' =>
//    string(29) "Can't find user field: field2"
//  }
//}
```

## Error handling
By default any error messages (except InvalidArgumentException in Sendios constructor) collects in error_log.
If you want the component throws exceptions just change handler mode:
```php
$sendios = new Sendios($clientId, $clientHash);
$sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
```

## Set curl options for single request
```php
$sendios = new Sendios($clientId, $clientHash);
$sendios->request->setOption(CURLOPT_TIMEOUT, 2);
```

## Set curl options for multiple requests (permanent)
```php
$sendios->request->setOption(CURLOPT_TIMEOUT_MS, 2000, true);
```

## Reset permanent curl options
```php
$sendios->request->resetPermanentOptions();
```
