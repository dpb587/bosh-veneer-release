# Wellness

This bundle provides the tools for monitoring and responding to the health changes within the system.

 *
 *


## Extension: Provider

Bundles may provide implement the `Check\ProviderInterface` to provide alternatives for fetching data relevant to checks.

#### `logsearch.shipper.hoststat`

Load host stats for comparison.

    {
        "provider": "logsearch.shipper.hoststat",
        "options": {
            
        }
    }

## Extension: Watcher

The watcher takes source data and evalutes it in comparison to configurable directives.


## Extension: Responder

The responder handles conditions which the watcher says are significant.

### Usage:


#### `bosh.deployment_manifest.modify`

Modify a specific deployment manifest...

    {
        "responder": "bosh.deployment_manifest.modify",
        "options": {
            "file": "path/bosh.yml",
            "property": "jobs[appsrv].instaces",
            "action": "+=",
            "value": "1"
        }
    }
