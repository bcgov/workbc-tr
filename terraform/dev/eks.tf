#Cluster role
resource "aws_iam_role" "eks-cluster-role" {
  name = "eks-cluster-role"
  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = [
          "sts:AssumeRole",
          "sts:TagSession"
        ]
        Effect = "Allow"
        Principal = {
          Service = "eks.amazonaws.com"
        }
      },
    ]
  })
}

#Cluster role policy
resource "aws_iam_role_policy_attachment" "eks-cluster-policy" {
  policy_arn = "arn:aws:iam::aws:policy/AmazonEKSClusterPolicy"
  role       = aws_iam_role.eks-cluster-role.name
}

#EKS cluster
resource "aws_eks_cluster" "workbc-cluster" {
  name = "workbc-cluster"
  access_config {
    authentication_mode = "API_AND_CONFIG_MAP"
  }
  role_arn = aws_iam_role.eks-cluster-role.arn
  vpc_config {
    subnet_ids = module.network.aws_subnet_ids.app.ids
  }
  depends_on = [
    aws_iam_role_policy_attachment.eks-cluster-policy,
    aws_iam_role.eks-cluster-role,
  ]
}

#EKS cluster addons
resource "aws_eks_addon" "vpc-cni-addon" {
  cluster_name = aws_eks_cluster.workbc-cluster.name
  addon_name   = "vpc-cni"
}

resource "aws_eks_addon" "kube-proxy-addon" {
  cluster_name = aws_eks_cluster.workbc-cluster.name
  addon_name   = "kube-proxy"
}

resource "aws_eks_addon" "pod-identity-addon" {
  cluster_name = aws_eks_cluster.workbc-cluster.name
  addon_name   = "eks-pod-identity-agent"
}

resource "aws_eks_addon" "coredns-addon" {
  cluster_name = aws_eks_cluster.workbc-cluster.name
  addon_name   = "coredns"
}

#EFS CSI role
resource "aws_iam_role" "efs-csi-role" {
  name = "efs-csi-role"

  assume_role_policy = jsonencode({
    Statement = [{
      Action = [
          "sts:AssumeRole",
          "sts:TagSession"
        ]
      Effect = "Allow"
      Principal = {
        Service = "pods.eks.amazonaws.com"
      }
    }]
    Version = "2012-10-17"
  })
}

#EFS CSI policy
resource "aws_iam_role_policy_attachment" "ec-AmazonEFSCSIDriverPolicy" {
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonEFSCSIDriverPolicy"
  role       = aws_iam_role.efs-csi-role.name
}

resource "aws_eks_addon" "aws-efs-csi-driver" {
  cluster_name = aws_eks_cluster.workbc-cluster.name
  addon_name   = "aws-efs-csi-driver"

  pod_identity_association {
    role_arn = aws_iam_role.efs-csi-role.arn
    service_account = "efs-csi-controller-sa"
  }
}

#Node group role
resource "aws_iam_role" "eks-ng-role" {
  name = "eks-ng-role"

  assume_role_policy = jsonencode({
    Statement = [{
      Action = "sts:AssumeRole"
      Effect = "Allow"
      Principal = {
        Service = "ec2.amazonaws.com"
      }
    }]
    Version = "2012-10-17"
  })
}

#Node group policies
resource "aws_iam_role_policy_attachment" "ng-AmazonEKSWorkerNodePolicy" {
  policy_arn = "arn:aws:iam::aws:policy/AmazonEKSWorkerNodePolicy"
  role       = aws_iam_role.eks-ng-role.name
}

resource "aws_iam_role_policy_attachment" "ng-AmazonEKS_CNI_Policy" {
  policy_arn = "arn:aws:iam::aws:policy/AmazonEKS_CNI_Policy"
  role       = aws_iam_role.eks-ng-role.name
}

resource "aws_iam_role_policy_attachment" "ng-AmazonEC2ContainerRegistryReadOnly" {
  policy_arn = "arn:aws:iam::aws:policy/AmazonEC2ContainerRegistryReadOnly"
  role       = aws_iam_role.eks-ng-role.name
}

#Node group
resource "aws_eks_node_group" "eks-ng" {
  cluster_name    = aws_eks_cluster.workbc-cluster.name
  node_group_name = "eks-ng"
  node_role_arn   = aws_iam_role.eks-ng-role.arn
  subnet_ids      = module.network.aws_subnet_ids.app.ids

  scaling_config {
    desired_size = 1
    max_size     = 2
    min_size     = 1
  }

  update_config {
    max_unavailable = 1
  }

  # Ensure that IAM Role permissions are created before and deleted after EKS Node Group handling.
  # Otherwise, EKS will not be able to properly delete EC2 Instances and Elastic Network Interfaces.
  depends_on = [
    aws_iam_role_policy_attachment.ng-AmazonEKSWorkerNodePolicy,
    aws_iam_role_policy_attachment.ng-AmazonEKS_CNI_Policy,
    aws_iam_role_policy_attachment.ng-AmazonEC2ContainerRegistryReadOnly,
  ]
}

#Fargate profile role
resource "aws_iam_role" "eks-fp-role" {
  name = "eks-fp-role"

  assume_role_policy = jsonencode({
    Statement = [{
      Action = "sts:AssumeRole"
      Effect = "Allow"
      Principal = {
        Service = "eks-fargate-pods.amazonaws.com"
      }
    }]
    Version = "2012-10-17"
  })
}

#Fargate profile policy
resource "aws_iam_role_policy_attachment" "fp-AmazonEKSFargatePodExecutionRolePolicy" {
  policy_arn = "arn:aws:iam::aws:policy/AmazonEKSFargatePodExecutionRolePolicy"
  role       = aws_iam_role.eks-fp-role.name
}

#Fargate profile
resource "aws_eks_fargate_profile" "workbc-fp" {
  cluster_name           = aws_eks_cluster.workbc-cluster.name
  fargate_profile_name   = "workbc-fp"
  pod_execution_role_arn = aws_iam_role.eks-fp-role.arn
  subnet_ids             = module.network.aws_subnet_ids.app.ids

  selector {
    namespace = "app"
  }
}
