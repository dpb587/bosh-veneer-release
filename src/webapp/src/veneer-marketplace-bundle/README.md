# Marketplaces

The "marketplace" concept is simply an external service which provides releases or stemcells.

 * browse releases and stemcells from bosh.io, AWS S3 buckets
 * upload releases and stemcells from the Veneer web interface
 * track when new versions are available


## Configuration

Currently, the following services can be used:

 * `bosh-hub` - for reading [bosh.io](https://bosh.io) or a self-hosted hub
 * `aws-s3` - for finding releases and stemcells in AWS S3 buckets

Configure the marketplaces with the `veneer_marketplace.marketplaces` setting. For example...

    veneer_marketplace:
      marketplaces:
        # use the public bosh.io hub
        boshio:
          type: "bosh-hub"

        # use a bucket with org releases
        acmecorp:
          type: "aws-s3"
          title: "ACME Corp - Stable"
          options:
            bucket: "acmecorp-bosh-artifacts"
            releases_prefix: "stable/releases/"
            stemcells_prefix: "stable/stemcells/"


## Snippets

The `aws-s3` marketplace type will need to be able to list the bucket and download the tarballs...

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
                    "arn:aws:s3:::example-release-us-west-2"
                ],
                "Condition": {
                    "StringLike": {
                        "s3:prefix": [
                            "stable/*"
                        ]
                    }
                }
            }
        ]
    }
