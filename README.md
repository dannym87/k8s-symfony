# Symfony Kubernetes

This project is merely a test app for my own personal use for getting a kubernetes cluster running a Symfony app on AWS.

More detailed instructions can be found at [https://aws.amazon.com/blogs/compute/kubernetes-clusters-aws-kops/](https://aws.amazon.com/blogs/compute/kubernetes-clusters-aws-kops/)

## AWS

Configure the AWS cli with your credentials

```
aws configure
```

### Create an S3 Bucket

Create an S3 bucket to store the kubernetes config

```
aws s3api create-bucket --bucket testing.digital-elements.co.uk --region eu-west-2 --create-bucket-configuration LocationConstraint=eu-west-2
```
Export the environment variable to set the kops S3 store

```
export KOPS_STATE_STORE=s3://testing.digital-elements.co.uk
```

### Route 53

Create a hosted zone for the DNS records

```
ID=$(uuidgen) && \                                                                                        
aws route53 create-hosted-zone \
--name testing.digital-elements.co.uk \
--caller-reference $ID \
| jq .DelegationSet.NameServers
```

## kops

Create the kubernetes cluster using `kops`

```
kops create cluster --master-size t2.micro --node-size t2.micro --zones eu-west-2a --ssh-public-key ~/.ssh/id_rsa.pub testing.digital-elements.co.uk --yes
```

Wait for the cluster to be ready

```
kops validate cluster
kubectl get nodes
```

## Build, tag & push the Docker image

```
docker build -t k8s-symfony .
docker tag k8s-symfony:latest xxx.dkr.ecr.eu-west-2.amazonaws.com/k8s-symfony:1.0.0
docker push xxx.dkr.ecr.eu-west-2.amazonaws.com/k8s-symfony:1.0.0
```

## Deploy via kubectl

### Deployment

```
kubectl apply -f kubernetes/app.deployment.yaml
```

### Service

```
kubectl apply -f kubernetes/app.lb.yaml
kubectl get services -o wide
```

### Scale the deployment

```
kubectl scale --replicas 4 -f kubernetes/app.deployment.yaml
```

todo: Check if there's a way for Kubernetes to automatically assign an ALIAS for the ELB to the Route53 DNS records

## Tear down the cluster

Completely remove the Kubernetes cluster

```
kops delete cluster --name=testing.digital-elements.co.uk --yes
```