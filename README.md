# kafka-php-test
Testing Kefka producer and consumer in PHP.

## Usage

````
docker-compose up
````

Wait until kafka container is running.

Start a consumer of the `testing` (default) topic.

````
docker-compose run console kafka:consumer
````

Produce 10 messages to the `testing` (default) topic. Automatically generate messages (UUID strings).

````
docker-compose run console kafka:producer --auto --limit 10
````

Read messages from STDIN
````
docker-compose run console kafka:producer
````

## Commands help

### Producer

````
Description:
  Send messages to kafka.

Usage:
  kafka:producer [options]

Options:
      --auto            Auto generate messages (uuids).
  -t, --topic=TOPIC     Send messages to specific topic. [default: "testing"]
  -p, --pause=PAUSE     Pause X microseconds between messages. [default: 0]
  -l, --limit=LIMIT     Limit number of messages to X [default: -1]
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
````

### Consumer

````
Description:
  Reads and prints messages from kafka.

Usage:
  kafka:consumer [options]

Options:
  -t, --topic=TOPIC               Receive messages from specific topic. [default: "testing"]
  -p, --pause=PAUSE               Pause X microseconds between messages. [default: 0]
  -h, --help                      Display this help message
  -q, --quiet                     Do not output any message
  -V, --version                   Display this application version
      --ansi                      Force ANSI output
      --no-ansi                   Disable ANSI output
  -n, --no-interaction            Do not ask any interactive question
  -ao, --auto-offset=AUTO-OFFSET  Where to start reading from (latest, earliest). [default: "latest"]
  -v|vv|vvv, --verbose            Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
````  

