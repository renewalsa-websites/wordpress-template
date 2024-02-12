---
layout: default
title: Storage (mounts)
parent: Working with Platform.sh
nav_order: 10
---

# Storage (mounts)

The 'standard' filesytem on Platform.sh is ready-only by default. Any directories or files that you wish to be writable must exist within pre-defined "mounts". These are persisted between deployments, getting detached and re-attached as new containers are created for each deployment.

Platform.sh also provides a simple way to synchronise these files across environments, making it trivial to update the `uat` environment with the uploads and DB from `live`.

For WordPress the main requirement is a writable directory for uploads.

Mounts and storage size are defined in `.platform.app.yaml`

Storage size standard definition - 
```yaml
# The size of the persistent disk of the application (in MB). Minimum value is 128.
# Default projects have 5GB of storage, we allocate 4608MB to mounts / uploads, and 512 to DB disk.
disk: 4608
```

{: .warning }
When setting the disk size, be sure to account for the DB disk size as well (as defined in `.platform/services.yaml`). The total size of the Mounts + DB must be equal to or less than the allocated project storage to successfully deploy.


Mounts standard definition - 
```yaml
# The following block defines a writable directory, 'site/content/uploads'
# The 'source' specifies where the writable mount is. The 'local' source
# indicates that the mount point will point to a local directory on the
# application container. The 'source_path' specifies the subdirectory
# from within the source that the mount should point at. 
mounts:
    'site/content/cache':
        source: local
        source_path: 'cache'
    'site/content/uploads':
        source: local
        source_path: 'uploads'
```

If your application requires other writable directories then they can be added to the above list - in the case of WordPress this is generally discouraged, as all "stateful" content should be in `uploads`.


## Accessing mounted files (uploads folder)

Similar the DB, it is common to have to upload the 'stateful' files of a WordPress installation, particularly when deploying for the first time or getting ready to go live.

{: .note }
This process is easier if done from your local machine due to the way authentication works. If necessary just `rsync` the data to your local from staging before using the local copy to push to platform.sh.

### Listing Mounts
Mounts are defined in `platform.app.yaml`, however you can always list out the active mounts for a project using the CLI.

```bash
platform mount:list --project=yourprojectid --environment=live
```

Take note of the "Mount path", as this will be used when we upload the files.

### Downloading files from a mount

Example: 

To download files, platform.sh uses the `mount:download` command, this uses `rsync` under the hood to ensure a fast, safe and predictable outcome when uploading the assets. The command takes two main flags `--target` for the destination of the files locally, and `--mount` for the path to the mounted filesystem in your app.

{:.warning}
As we are dealing with paths here, be sure to adjust and correct the paths for your project. Do not use trailing slashes on the paths for --target and --mount, as this can have unintended effects.

```bash
# This example presumes you are running the default directory structure of this project as
# specified in this repo. 

# The `--source` path is relative and the command presumes you are in the root directory  
# of the project.

platform mount:upload --mount site/content/uploads --target site/content/uploads --project=yourprojectid --environment=live
```

### Uploading files to a mount

To upload our files, platform.sh uses the `mount:upload` command, this uses `rsync` under the hood to ensure a fast, safe and predictable outcome when uploading the assets. The command takes two main flags `--source` for the path to the files locally, and `--mount` for the path to the mounted filesystem in your app.

{:.warning}
As we are dealing with paths here, be sure to adjust and correct the paths for your project. Do not use trailing slashes on the paths for --source and --mount, as this can have unintended effects.

Example: 
```bash
# This example presumes you are running the default directory structure of this project as
# specified in this repo. 

# The `--source` path is relative and the command presumes you are in the root directory  
# of the project.

platform mount:upload --mount site/content/uploads --source site/content/uploads --project=yourprojectid --environment=live
```
