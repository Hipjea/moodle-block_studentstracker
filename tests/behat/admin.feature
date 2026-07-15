@block @block_studentstracker
Feature: Admin tests for Students tracker block
  In order to use Students tracker block in a course
  As a user
  I need to configure a Students tracker block

  Background:
    Given I log in as "admin"

  @javascript
  Scenario: Plugin block_studentstracker appears in the list of installed additional plugins
    When I navigate to "Plugins > Plugins overview" in site administration
    And I follow "Additional plugins"
    Then I should see "Students tracker"
    And I should see "block_studentstracker"

  Scenario: Admin can access the Students tracker global settings page
    When I navigate to "Plugins > Blocks > Students tracker" in site administration
    Then I should see "Number of days to start tracking"
    And I should see "Critical limit (in days)"
    And I should see "Days color"
    And I should see "Critical days color"
    And I should see "No access color"
    And I should see "Roles to track"
    And I should see "Show only n results"
    And I should see "Exclude results older than n days"
    And I should see "Date format"
    And I should see "Sorting criteria"
    And I should see "Abbreviate first names"
