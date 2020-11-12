Feature: Send Mail

  Scenario: Sending valid mail
    Given There is a valid mail
    When I send the mail
    Then It should be put in a queue to be sent asynchronously

  Scenario: Retrying a not delivered message
    Given there is a problem in the network
    When I send the mail
    Then the mail should fail
    And the sending should be retried

  Scenario: If a mail service is unavailable there should be a fallback to a secondary service and so on
    Given a mail service is unavailable
    When I send the mail
    Then the mail should fail
    And it should be se sent through another mail service available

  Scenario: There should be possible to assign new fallbacks
    Given there I have a new valid mail service creation requisition
    When I request to create the mail service
    Then A new fallback should be put on the fallback structure

  Scenario: It should be able to use the API by A JSON HTTP Request
    Given There is a valid mail
    And It the Client is sending it through a HTTP Request in JSON format
    When I send the mail
    Then the response should be success
    And the mail sent

  Scenario: It should be able to use the API through a CLI command
    Given There is a valid mail
    And It the Client is sending it through a CLI
    When I send the mail
    Then the response should be success
    And the mail sent

  Scenario: Every mail that is sent should leave a log entry
    Given There is a valid mail
    When I send the mail
    Then a log entry should be leave specifying the transaction
