<?php
$message = 'Teste ' . microtime() . ' ';
const MAX = 50;

$conf = new \RdKafka\Conf();
$conf->setErrorCb(function ($kafka, $err, $reason) {
    //
});
$conf->setDrMsgCb(function ($kafka, $message) {
    //
});

$producer = new \RdKafka\Producer($conf);
$producer->setLogLevel(LOG_DEBUG);

if ($producer->addBrokers("kafka:29092") < 1) {
    echo "Failed adding brokers\n";
    exit;
}

$topicConf = new \RdKafka\TopicConf();
$topicConf->set("message.timeout.ms", 2000);

/** @var \RdKafka\ProducerTopic $topic */
$topic = $producer->newTopic("sample", $topicConf);

if (!$producer->getMetadata(false, $topic, 2000)) {
    echo "Failed to get metadata, is broker down?\n";
    exit;
}

for ($i = 0; $i < MAX; $i++) {
    $topic->produce(
        RD_KAFKA_PARTITION_UA, 0, 
        $message . $i, sha1(microtime())
    );
}

$producer->poll(0);
while($producer->getOutQlen() > 0) {
    $producer->poll(50);
}

echo "Message published\n";