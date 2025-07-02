resource "aws_iam_policy" "alb_policy" {
  name   = "AWSLoadBalancerControllerIAMPolicy"
  policy = file("${path.module}/iam_policy.json")
}

resource "aws_iam_role" "alb_role" {
  name = "AWSLoadBalancerControllerIAMRole"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
            "Effect": "Allow",
            "Principal": {
                "Service": "pods.eks.amazonaws.com"
            },
            "Action": [
                "sts:AssumeRole",
                "sts:TagSession"
            ]
      }
    ]
  })
}

resource "aws_iam_role_policy_attachment" "alb_attachment" {
  role       = aws_iam_role.alb_role.name
  policy_arn = aws_iam_policy.alb_policy.arn
}

