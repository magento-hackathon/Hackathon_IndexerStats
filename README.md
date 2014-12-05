Hackathon_IndexerStats
======================

This Magento extension adds a column to the indexer list with information about running time for full reindex of the
respective indexers. The remaining time of a running indexer is estimated based on previous running times.

![Screenshot](https://github.com/magento-hackathon/Hackathon_IndexerStats/raw/master/screenshot-progress.png)

Compatibility
----
- Magento CE 1.9
- Magento CE 1.8
- Magento CE 1.7

*requires PHP 5.4 or higher*

Installation
----

1. Manual installation: download [the latest release](https://github.com/magento-hackathon/Hackathon_IndexerStats/zipball/master) and copy the directories `app`, `js` and `skin` into the
Magento installation.
2. via Composer:

        "require": {
            "magento-hackathon/indexer-stats": "dev-master"
        }


Open Issues
----

1. The extension is not compatible with Magento Enterprise yet


Use with Custom Indexers
----

The statistics are gathered with an observer for the events `after_reindex_process_$INDEXERCODE` with `$INDEXERCODE` being
the code of the indexer. Unfortunately the observer has to be registered for each indexer explicitely. So if you want to
use it with a custom indexer, add the following to your `config.xml` (replace `EXAMPLE` with your indexer code):

        <config>
            <global>
                <events>
                    <after_reindex_process_EXAMPLE>
                        <observers>
                            <hackathon_indexerstats>
                                <class>hackathon_indexerstats/observer</class>
                                <method>saveHistory</method>
                            </hackathon_indexerstats>
                        </observers>
                    </after_reindex_process_EXAMPLE>
                </events>
            </global>
        </config>


License
----

Open Software License ("OSL") v. 3.0

Authors
----

- Dima Janzen [@dimajanzen](http://twitter.com/dimajanzen)
- Fabian Schmengler [@schmengler](http://twitter.com/fschmengler)