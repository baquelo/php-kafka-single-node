<?php

$consumer = new \RdKafka\Consumer();
$consumer->setLogLevel(LOG_DEBUG);
$consumer->addBrokers("kafka:29092");

$topic = $consumer->newTopic("sample");

$topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

echo "consumer started";
while (true) {
    $msg = $topic->consume(0, 1000);
    if ($msg->payload) {
        echo $msg->payload, "\n";
    }
}