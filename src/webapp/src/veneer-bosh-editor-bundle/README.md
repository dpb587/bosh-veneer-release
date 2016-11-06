# Operations

This bundle provides web-based tools for creating and modifying deployments from veneer. It helps...

 * provide web forms for configuring deployment pools, networks, jobs, and properties
 * configure deployment properties
 * upload and delete releases, stemcells, and deployments
 * track changes made to deployment configurations through version control
 * draft proposed changes before executing a deployment
 * stop/start/restart jobs and run errands


## Version Control

For versioning `git` is used and the repository can be pushed/pulled from `/bosh-editor/repo.git`.


## Terminology and Structure



A prefix path can be configured to allow using a single repository for multiple purposes, but within the configured root
the following hierarchy is used:

 * **/_compiled** - veneer
    * **/{namespace}**
       * **/bosh.yml** - a fully compiled deployment manifest
 * **/{namespace}** - a specific namespace
    * **/bosh.yml** -
