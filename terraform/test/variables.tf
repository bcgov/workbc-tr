# variables.tf

variable "target_env" {
  description = "AWS workload account env (e.g. dev, test, prod, sandbox, unclass)"
  default = "test"
}

variable "aws_region" {
  description = "The AWS region things are created in"
  default     = "ca-central-1"
}

variable "app_name" {
  description = "Name of the application"
  type        = string
  default     = "workbc-cer"
}

variable "app_image" {
  description = "Docker image to run in the ECS cluster. _Note_: there is a blank default value, which will cause service and task resource creation to be supressed unless an image is specified."
  type        = string
  default     = ""
}

variable "app_repo" {
  description = "ECR docker image repo"
  type        = string
  default     = ""
}

variable "app_port" {
  description = "Port exposed by the docker image to redirect traffic to"
  default     = 443
}

variable "app_count" {
  description = "Number of docker containers to run"
  default     = 1
}


variable "health_check_path" {
  default = "/index.html"
}

variable "fargate_cpu" {
  description = "Fargate instance CPU units to provision (1 vCPU = 1024 CPU units)"
  default     = 2048
}

variable "fargate_memory" {
  description = "Fargate instance memory to provision (in MiB)"
  default     = 4096
}

variable "common_tags" {
  description = "Common tags for created resources"
  default = {
    Application = "Career Discovery Quizzes"
  }
}

variable "service_names" {
  description = "List of service names to use as subdomains"
  default     = ["workbc-cer"]
  type        = list(string)
}

variable "alb_name" {
  description = "Name of the internal alb"
  default     = "default"
  type        = string
}

variable "cloudfront" {
  description = "enable or disable the cloudfront distrabution creation"
  type        = bool
}

variable "cloudfront_origin_domain" {
  description = "domain name of the app"
  type        = string
}
