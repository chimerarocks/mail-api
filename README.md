<h1>Mail API</h1>

## Motivation

A API to send transaction emails with a high degree of certainty.

- HTTP or CLI
- Send emails using Markdown, text or HTML format
- Powerful Service Mailers Fallback engine

## Code Design

#### Domain
<i>app/Domain</i>

The domain is where are the business rules. Here we have framework agnostic classes.

This is because we don't want to dirty our domain with non-domain-relevant code. 

Let's say that we want to change the framework from Laravel to ZendExpressive, our domain code
should be the same, without changes.

Basically the design it's about those 3 models:
* SendMailService: A service to send emails through Mailers that are supported by a Circuit Breaker structure.
* Mailer: An interface to use an external mail service 
* MailerCircuitBreaker: An interface to coordinate Mailers in case of failure.

###### What is a Circuit Breaker?
There is a beautiful explanation, with graphs here: https://github.com/ackintosh/ganesha/

A circuit breaker acts mediating each request to external services, if the request fails, it
will handle the requests without calling the service anymore while verifying when the service is 'up' 
again. 

#### Application
<i>app/Application</i>

The application layer is where are the Laravel framework related code.
Like handling Http requests, exceptions, queueing...

#### Infrastructure
<i>app/Infrastructure</i>

The infrastructure layer is where are the adapters to integrate the domain
with external tools.
Like mail services, the circuit breaker...

So, for example, in order to integrate with a new mail service we would create
a new Mailer class.
```php
<?php
//app/Infrastructure/Mailers
declare(strict_types=1);

namespace App\Infrastructure\Mailers;

use App\Domain\Services\Mailer;
use App\Domain\Values\Mail;

class NewMailer implements Mailer {
    public function getName() : string {
     // TODO: Implement getName() method.
    }
    public function send(Mail $mail) : void {<
     // TODO: Implement send() method.
    }
}
```

## Packages

* Laravel: Application framework
* Ganesha: Php package implementing Circuit Breaker pattern
* Mailjet: Mailjet driver to send emails
* Sendgrid: Sendgrid driver to send emails

## Testing

Structure:
* API -> Test the API specification contract: HTTP verbs, Request and Response Data, Validations...
* Feature -> Test Business rules: The requirements 
given in order to validate that the api accomplished what it was requested
* Unit -> Unit tests
* Data -> Some test specific data classes
* Mock -> Mocking services to test isolation
* Traits -> Helpers to avoid duplication of code. 

## Running

This are the vars that you will need to set in order to use Mailjet and SendGrid

    MAIL_PASS=
    MAILJET_KEY=
    MAILJET_SECRET=
    SENDGRID_API_KEY=

There are two envinroments to run the application:

Environment 1:
    <i>The purpose of this environment is to use the application only with Docker, without having to install anything 
    else</i>
    
In this case the application will use sqlite and apcu and use docker multi stage build
    
At the root path of the project
    
    ```sh
    cp .env.solo.example .env
    touch database.sqlite
    docker build -t mail-api .
    docker run --rm -p 9000:8000 mail-api 
    ```
    
The API will be open at port 9000.

Environment 2:
    <i>This is a more complete environment using docker-compose, nginx, php-fpm, redis and mysql </i>
    
    ```sh
    cp .env..example .env
    docker-compose up
    ```
The API will be open at port 8000.

    
<i>"BUT THERE IS NO NEED FOR DATABASE IN THIS APPLICATION!" - you will say. </i>

That is true, but in order to exemplify how would be using a database with docker and tests, I let it here.

## Sending emails

See the documentation accessible at /api/v1/doc.

But basically:

POST /api/v1/mail
```json
{
    "from": "no-reply@server.com",
    "to": "jondoe@foo.com",
    "cc": ["jondoe@foo.com"],
    "format": "markdown",
    "subject": "Subject of the email",
    "body": "Body text"
}
```


