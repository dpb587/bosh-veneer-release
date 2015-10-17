Sample AWS S3 IAM...

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
