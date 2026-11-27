<?php
// Runs against the real API when DELIVRIE_TEST_KEY is set (the Live API test workflow sets it). php tests/live.php
declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class, 'Delivrie\\')) {
        require __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, 9)) . '.php';
    }
});

$key = getenv('DELIVRIE_TEST_KEY');
if (!$key) {
    echo "DELIVRIE_TEST_KEY not set, skipped\n";
    exit(0);
}
$from = getenv('DELIVRIE_TEST_FROM') ?: 'Delivrie CI <onboarding@delivrie.io>';
$run = getenv('GITHUB_RUN_ID') ?: (string) time();
$d = new Delivrie\Delivrie($key);
$fail = 0;
$check = static function (string $name, callable $fn) use (&$fail): void {
    try { $fn(); echo "ok    {$name}\n"; } catch (Throwable $e) { $fail++; echo "FAIL  {$name}\n      {$e->getMessage()}\n"; }
};
$mail = static fn (string $subject) => ['from' => $from, 'to' => 'delivered@test.delivrie.io', 'subject' => "[CI {$run}] {$subject}", 'text' => "Live API test {$run}"];

$check('PHP SDK: send', function () use ($d, $mail) {
    $r = $d->emails->send($mail('PHP SDK send'));
    if (strlen($r['id'] ?? '') < 10) throw new RuntimeException('no id: ' . json_encode($r));
});
$check('PHP SDK: idempotent retry returns the same email', function () use ($d, $mail, $run) {
    $k = "php-live-{$run}-" . microtime(true);
    $a = $d->emails->send($mail('PHP idempotent'), ['idempotency_key' => $k]);
    $b = $d->emails->send($mail('PHP idempotent'), ['idempotency_key' => $k]);
    if ($a['id'] !== $b['id']) throw new RuntimeException("{$a['id']} then {$b['id']}");
});
$check('PHP SDK: API errors throw DelivrieException', function () use ($d, $from) {
    try { $d->emails->send(['from' => $from, 'to' => 'delivered@test.delivrie.io', 'subject' => 'no body']); throw new RuntimeException('no exception'); }
    catch (Delivrie\Exception\DelivrieException $e) { if ($e->getErrorName() !== 'validation_error') throw new RuntimeException($e->getErrorName() . ': ' . $e->getMessage()); }
});
exit($fail ? 1 : 0);
