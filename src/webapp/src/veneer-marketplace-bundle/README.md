# Marketplaces

This bundle centralizes access to releases and stemcells from external services. It helps...

 * browse releases and stemcells from bosh.io, AWS S3 buckets
 * upload releases and stemcells from veneer
 * realize when deployments are out of date


## Configuration

Currently, the following built-in services can be used:

 * `bosh-hub` - for reading [bosh.io](https://bosh.io) or a self-hosted hub
 * `aws-s3` - for finding releases and stemcells in AWS S3 buckets

Configure the marketplaces with the `veneer_marketplace.marketplaces` setting. For example...

    veneer_marketplace:
      marketplaces:
        # use the public bosh.io hub
        boshio:
          type: "bosh-hub"

        # use a s3 bucket with corporate releases
        acmecorp:
          type: "aws-s3"
          title: "ACME Corp - Stable"
          options:
            bucket: "acmecorp-bosh-artifacts-us-east-1"
            access_key_id: ~
            secret_access_key: ~


### AWS S3

This marketplace assumes the following defaults:

 * the bucket resides in `us-east-1` (via `region` option)
 * releases live somewhere in `release/` (via `release_prefix` option)
 * releases are named `/{name}-{version}.tgz` (via `release_regex` option)
    * e.g. `/{name}/{version}.tgz` -> `#/(?P<name>[^/]+)/(?P<version>\d.*)\.tgz$#`
 * stemcells live somewhere in `stemcell/` (via `stemcell_prefix` option)
 * stemcells are named `/{name}-{version}(-light)?.tgz` (via `stemcell_regex` option)
    * e.g. `/light-{name1}-stemcell-{version}-{name2}.tgz` -> `#/(?<light>light\-)(?P<name1>bosh\-)stemcell\-(?P<version>\d.*)\-(?P<name>[^/]+)\.tgz$#`

The S3 client requires permissions to list the bucket and retrieve the tarballs. You might want to use an IAM policy
like the following...

    {
        "Version": "2012-10-17",
        "Statement": [
            {
                "Effect": "Allow",
                "Action": [
                    "s3:GetObject",
                    "s3:ListBucket"
                ],
                "Resource": [
                    "arn:aws:s3:::acmecorp-bosh-artifacts-us-east-1"
                ],
                "Condition": {
                    "StringLike": {
                        "s3:prefix": [
                            "release/*",
                            "stemcell/*"
                        ]
                    }
                }
            }
        ]
    }
