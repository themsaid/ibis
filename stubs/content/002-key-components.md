## Key Components

>{notice} This is a sample content from [Laravel Queues in Action](https://learn-laravel-queues.com/). A book by [Mohamed Said](https://twitter.com/themsaid) the creator of Ibis.

A queue system has 3 main components; queues, messages, and workers.

Let's start by exploring each of these components.

### Queues

>{quote} A queue is a linear data structure in which elements can be added to one end, and can only be removed from the other end.

That's the definition you find in most Computer Science books, and it can be quite confusing.

Let me show you a queue in action:

```php
$queue = [
    'DownloadProject',
    'RunTests'
];
```

This queue contains two messages. We can `enqueue` a new message and it'll be added to the end of the queue:

```php
enqueue('Deploy');

$queue = [
    'DownloadProject',
    'RunTests',
    'Deploy'
];
```

We can also `dequeue` a message and it'll be removed from the beginning of the queue:

```php
$message = dequeue(); // == DownloadProject

$queue = [
    'RunTests',
    'Deploy'
];
```

If you ever heard the term "first-in-first-out (FIFO)" and didn't understand what it means, now you know it means the first message in the queue is the first message that gets processed.

### Messages

A message is a call-to-action trigger. You send a message from one part of your application and it triggers an action in another part. Or you send it from one application and it triggers an action in a completely different application.

The message sender doesn't need to worry about what happens after the message is sent, and the message receiver doesn't need to know who the sender is.

A message body contains a string, this string is interpreted by the receiver and the call to action is extracted.

### Workers

A worker is a long-running process that dequeues messages and executes the call-to-action.

Here's an example worker:

```php
while (true) {
    $message = dequeue();

    processMessage(
        $message
    );
}
```

Once you start a worker, it'll keep dequeuing messages from your queue. You can start as many workers as you want; the more workers you have, the faster your messages get dequeued.

## Queues in Laravel

Laravel ships with a powerful queue system right out of the box. It supports multiple drivers for storing messages:

- Database
- Beanstalkd
- Redis
- Amazon SQS

Enqueuing messages in Laravel can be done in several ways, and the most basic method is using the `Queue` facade:

```php
use Illuminate\Support\Facades\Queue;

Queue::pushRaw('Send invoice #1');
```

If you're using the database queue driver, calling `pushRaw()` will add a new row in the `jobs` table with the message "Send invoice #1" stored in the `payload` field.

Enqueuing raw string messages like this is not very useful. Workers won't be able to identify the action that needs to be triggered. For that reason, Laravel allows you to enqueue class instances:

```php
use Illuminate\Support\Facades\Queue;

Queue::push(
    new SendInvoice(1)
);
```

>{notice} Laravel uses the term "push" instead of "enqueue", and "pop" instead of "dequeue".

When you enqueue an object, the queue manager will serialize it and build a string payload for the message body. When workers dequeue that message, they will be able to extract the object and call the proper method to trigger the action.

Laravel refers to a message that contains a class instance as a "Job". To create a new job in your application, you may run this artisan command:

```php
php artisan make:job SendInvoice
```

This command will create a `SendInvoice` job inside the `app/Jobs` directory. That job will look like this:

```php
namespace App\Jobs;

class SendInvoice implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        
    }

    public function handle()
    {
        // Execute the job logic.
    }
}
```

When a worker dequeues this job, it will execute the `handle()` method. Inside that method, you should put all your business logic.

>{notice} Starting Laravel 8.0, you can use `__invoke` instead of `handle` for the method name. 

### The Command Bus

Laravel ships with a command bus that can be used to dispatch jobs to the queue. Dispatching through the command bus allows us to use several functionalities that I'm going to show you later.

Throughout this book, we're going to use the command bus to dispatch our jobs instead of the `Queue::push()` method. 

Here's an example of using the `dispatch()` helper which uses the command bus under the hood:

```php
dispatch(
    new SendInvoice(1)
);
```

Or you can use the `Bus` facade:

```php
use Illuminate\Support\Facades\Bus;

Bus::dispatch(
    new SendInvoice(1)
);
```

You can also use the `dispatch()` static method on the job class:

```php
SendInvoice::dispatch(1);
```

>{notice} Arguments passed to the static `dispatch()` method will be transferred to the job instance automatically.

### Starting A Worker

To start a worker, you need to run the following artisan command:

```shell
php artisan queue:work
```

This command will bootstrap an instance of your application and keep checking the queue for jobs to process.

```php
$app = require_once __DIR__.'/bootstrap/app.php';

while (true) {
    $job = $app->dequeue();

    $app->process($job);
}
```

Re-using the same instance of your application has a major performance gain as your server will have to bootstrap the application only once during the time the worker process is alive. We'll talk more about that later.

## Why Use a Message Queue?

Remember that queuing is a technique for indirect program-to-program communication. Your application can send messages to the queue, and other parts of the application can read those messages and execute them.

Here's how this can be useful:

### Better User Experience

The most common reason for using a message queue is that you don't want your users to wait for certain actions to be performed before they can continue using your application.

For example, a user doesn't have to wait until your server communicates with a third-party mail service before they can complete a purchase. You can just send a success response so the user continues using the application while your server works on sending the invoice in the background.

### Fault Tolerance

Queue systems are built to persist jobs until they are processed. In the case of failure, you can configure your jobs to be retried several times. If the job keeps failing, the queue manager will put it in safe storage so you can manually intervene.

For example, if your job interacts with an external service and it went down temporarily, you can configure the job to retry after some time until this service is back online.

### Scalability

For applications with unpredictable load, it's a waste of money to allocate more resources all the time even when there's no load. With queues, you can control the rate at which your workers consume jobs.

Allocate more resources and your jobs will be consumed faster, limit those resources and your jobs will be consumed slower but you know they'll eventually be processed.

### Batching

Batching a large task into several smaller tasks is more efficient. You can tune your queues to process these smaller tasks in parallel which will guarantee faster processing.

### Ordering and Rate Limiting

With queues, you can ensure that certain jobs will be processed in a specific order. You can also limit the number of jobs running concurrently.
