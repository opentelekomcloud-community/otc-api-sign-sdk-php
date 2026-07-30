# FunctionGraph sample to start ECS

This sample shows how to use functiongraph start to an ECS instance.


## Create deployment package

Use Composer to create deployment package:

```bash
composer archive --format=zip --file=code
```

## Create Functiongraph function

In [FunctionGraph console](https://console.otc.t-systems.com/functiongraph) create function with following settings:

- **Create With**: `Create from scratch`
- **Function Type**: `Event Function`
- **Function Name**: `<Your Function Name>`
- **Region**: `<Your Region>`
- **Agency**: Specify an agency with ECS permissions, e.g. `ECS User`
- **Runtime**: `PHP 8.3`

## Upload code.zip

In FunctionGraph Code  -> Code Source upload `code.zip`

## Configure function

In Configuration Tab -> Basic Settings:

- **Handler name**: `src/index.handler`

In Configuration Tab -> Environment Variables:

Add following variables:

| Env variable    | Description       | Sample
| --------------- | ----------------- | ---------------
| ECS_ENDPOINT    | endpoint of ECS   | ecs.eu-de.otc.t-systems.com
| ECS_INSTANCE_ID | Instance ID of ECS | cdb29bdd-1235-4e98-90d3-34bb77450393


## Test

Create a any test event and click `Test` to test the function.

