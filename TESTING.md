DrupalCI - Test Runner
======================

Running the test runner tests
-----------------------------

There is currently no continuous integration set up for drupalci_testbot. Run tests locally, please.

At this point, the tests for drupalci are somewhat coupled to the environment, so we must start clean:

- Use a native environment, either on a testbot machine or using the virtual machine provided by vagrant or similar.
- Clear out all config. This is easiest to accomplish by removing `~/.drupalci`.
- Run the tests using `./bin/phpunit`.
- Depending on your needs, you can exclude some tests with hard dependencies with `--exclude-group`. Notably `@group docker`.

        $ git clone --branch dev https://git.drupal.org/project/drupalci_testbot.git
        $ cd drupalci_testbot
        $ vagrant up
        // Wait a while...
        $ vagrant ssh
        $ rm -rf ~/.drupalci
        $ ./drupalci init
        // Pull down all the images.
        $ cd /tmp
        $ git clone --branch 8.1.x https://git.drupal.org/project/drupal.git
        $ cd drupal
        $ ~/drupalci_testbot/bin/composer install
        $ cd ~/drupalci_testbot
        $ ./bin/phpunit
        // Tests run.
        $ ./bin/phpunit --exclude-group docker
        // Tests run without docker dependencies.
        $
