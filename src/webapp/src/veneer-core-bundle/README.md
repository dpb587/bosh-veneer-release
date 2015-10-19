# Core

This bundle provides some of the shared components that other bundles need to build off of. Those are documented
below...


## Workspace

We'll assume that some bundles will want to store and version their configuration somehow. For example, the ops bundle
needs to store and update deployment manifests.

### Repository

This tool uses a `git` repository to accomplish this, allowing users to clone, fetch to, and push from their local
workstations as a form of backup and to support lower-level functionality and configuration that bundles may not
currently support.

For CLI access, the workspace is available at:

    https://{host}/core/workspace.git

By default, the workspace root will be the root of the repository. However, this can be reconfigured to use a
subdirectory in cases where the repository has multiple purposes or inclues multiple environments. For example:

    bosh_core:
        workspace:
            root: prod/com.amazon.aws.us-west-2.cfmain/

The root is prefixed before all file operations performed by bundles, however relative symlinks will still be followed
which means they can be (ab)used to share files across roots. For example:

    prod/
        com.example.cfmain/               # global deployment
            secrets.yml                   # cross-region
        com.amazon.aws.us-east-1.cfmain/  # director in us-east-1
            com.example.cfmain/           # regional deployment
                secrets.yml               # ../../com.example.cfmain/secrets.yml
                bosh.yml                  # regional manifest
        com.amazon.aws.us-west-1.cfmain/  # director in us-west-1
            com.example.cfmain/           # regional deployment
                secrets.yml               # ../../com.example.cfmain/secrets.yml
                bosh.yml                  # regional manifest

Within the root, directories are used to distinguish between deployments (in general). Directory names should be


### Watcher

Bundles may claim individual files based on their path. For example, the ops bundle will claim files named `bosh.yml` as
deployment manifests. When files are modified in the repository on any branch, the respective bundles are notified
allowing them to review, compare, and queue further processing. The `WorkspaceWatcher` class is responsible for this.
A bundle can register its interest via dependency injection, specifying the path, callback method, and optional
priority...

    <service id="bosh_ops.workspace_watcher" ...>
        ...
        <tag name="bosh_core.workspace_watcher" path="#/bosh\.yml$#" method="onDeploymentManifest" />
    </service>

Once a branch is updated, the following steps are taken...

 0. list files which changed between old and new refs
 0. for each changed file, find the highest priority workspace watcher matching the path
 0. for all watched files, sort by priority (highest first), then by lexical path
 0. for sorted files, execute callbacks

Callbacks are executed with a WorkspaceChangeEvent including the following details...

 * `branch`
 * `commit`
 * `path`
 * `change` - `created`, `modified`, `deleted`


### Environment

Some bundles using the workspace may want to expose details about their files to other files. For example, the ops
bundle provides a simple, file-based key-value store for things like authentication keys, and deployment manifests may
want to include those values as properties.

Bundles may expose their information via dependency injection, specifying the scope they provide...

    <service id="bosh_ops.workspace_environment.kv" ...>
        ...
        <tag name="bosh_core.workspace_environment" scope="kv" />
    </service>

Within supported templates, the `env` variable provides access to the data. It is treated like an array with key names
starting with the scope and optionally followed by a colon and additional resolving data. For example, the `kv` scope
provided by the ops bundle uses the additional data to determine which file (i.e. directory, if provided, otherwise from
the current deployment). With the ops' kv example...

    {{ env['kv']['ssl_key'] }}                         -> would load the `ssl_key` value
                                                       -> from prod/com.amazon.aws.us-east-1.cfmain/com.example.cfmain/kv.yml
                                                       -> when compiling prod/com.amazon.aws.us-east-1.cfmain/com.example.cfmain/bosh.yml
    {{ env['kv:com.example']['company_name']           -> would load the `company_name` value
                                                       -> from prod/com.amazon.aws.us-east-1.cfmain/com.example/kv.yml
                                                       -> when compiling prod/com.amazon.aws.us-east-1.cfmain/com.example.cfmain/bosh.yml
    {{ env['kv:com.example/contact']['admin']['email'] -> would load the `email` from the `admin` value
                                                       -> from prod/com.amazon.aws.us-east-1.cfmain/com.example/kv-contact.yml
                                                       -> when compiling prod/com.amazon.aws.us-east-1.cfmain/com.example.cfmain/bosh.yml

Whenever a scope is requested, the service is invoked with the source path, the post-colon resolving data, and caller
type (e.g. `bosh.deployment`). The service may return whatever data is appropriate.
