@block @block_studentstracker
Feature: Basic tests for Students tracker block
  In order to use Students tracker block in a course
  As a user
  I need to configure a Students tracker block and see it in a course

  Background:
    Given the following "users" exist:
      | username    | firstname | lastname | email            |
      | teacher1    | Terry1    | Teacher1 | teacher1@example.com |
      | assistant1  | Terry2    | Teacher2 | teacher2@example.com |
      | student1    | Sam1      | Student1 | student1@example.com |
      | student2    | Sam2      | Student2 | student2@example.com |
      | student3    | Sam3      | Student3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user        | course | role           |
      | teacher1    | C1     | editingteacher |
      | assistant1  | C1     | teacher        |
      | student1    | C1     | student        |
      | student2    | C1     | student        |
      | student3    | C1     | student        |

  @javascript
  Scenario: Plugin block_studentstracker appears in the list of installed additional plugins
    Given I log in as "admin"
    When I navigate to "Plugins > Plugins overview" in site administration
    And I follow "Additional plugins"
    Then I should see "Students tracker"
    And I should see "block_studentstracker"

  Scenario: Add the Students tracker block to a course.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Students tracker" block
    Then I should see "Sam1 Student1" in the "Students tracker" "block"
    And I should see "Sam2 Student2" in the "Students tracker" "block"
    And I should see "Sam3 Student3" in the "Students tracker" "block"

  Scenario: See on the Students tracker block that a student has accessed.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Students tracker" block
    And I should see "Sam1 Student1" in the "Students tracker" "block"
    And I should see "Sam2 Student2" in the "Students tracker" "block"
    And I should see "Sam3 Student3" in the "Students tracker" "block"
    And I log out
    And I am on the "C1" "Course" page logged in as "student1"
    And I log out
    And I am on the "C1" "Course" page logged in as "teacher1"
    Then I should see "Sam2 Student2" in the "Students tracker" "block"
    And I should see "Sam3 Student3" in the "Students tracker" "block"
    But I should not see "Sam1 Student1" in the "Students tracker" "block"

  Scenario: Only users with a tracked role are listed on the Students tracker block.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Students tracker" block
    Then I should see "Sam1 Student1" in the "Students tracker" "block"
    And I should see "Sam2 Student2" in the "Students tracker" "block"
    And I should see "Sam3 Student3" in the "Students tracker" "block"
    But I should not see "Terry1 Teacher1" in the "Students tracker" "block"
    And I should not see "Terry2 Teacher2" in the "Students tracker" "block"

  Scenario: Students who have never accessed the course are flagged as absent.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Students tracker" block
    Then I should see "potentially absent users" in the "Students tracker" "block"
    And I should see "no access" in the "Students tracker" "block"
    And I should see "Contact them." in the "Students tracker" "block"

  Scenario: A student cannot see the results of the Students tracker block.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Students tracker" block
    And I should see "Sam2 Student2" in the "Students tracker" "block"
    And I log out
    When I am on the "C1" "Course" page logged in as "student1"
    Then I should not see "Sam2 Student2"
    And I should not see "Sam3 Student3"

  Scenario: The Students tracker block title can be customised.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Students tracker" block
    When I configure the "Students tracker" block
    And I set the field "Block title" to "Absent learners"
    And I press "Save changes"
    Then I should see "Absent learners"
