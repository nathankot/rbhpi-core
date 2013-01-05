# The RBHPi Framework.

_Currently under active development. API **will** change without notice._

This is my personal PHP5.4+ framework. Humble and down to earth. It is loosely based around a HMVMC architectural pattern (Hierarchial Model—View-Model—Controller).

This framework is actually a _stripped-down and cleaned up_ version of my RBHP framework which I have been using in production since forever. It is designed to be released to the public so that I can feel like I've contributed to the Open Source community.

Although, given the excess amount of PHP Frameworks available out there, I highly doubt that the popularity of this framework will pick up any time soon. I also wouldn't be able to tell you how different or how this framework would stand out from the others, as I have never truly used a different framework in production.

Anyhow, this encompasses my personal way of doing things. See if you like it ;)

## Qualities of the Framework:

- Keep it simple.
- Make as much use of PHP's core functionality as possible.
- Compliance with FIG Standards.
- Disregard 'speed' and 'micro-optimizations' in favor of readability and elegance.
- Leave room for future adaptability and extensibility.

## Some cool things this framework can do:

### HMVC Request Injection

You can take a `Core\Prototype\Request` object, and inject it into another RBHPi server by doing something like this:

```php
$request = new \Core\Prototype\Request([
		'path' => '/route/to/your/request'
	,	'payload' => $data
	,	'host' => 'anotherserver.rbhpi.com'
	,	'method' => 'get'
	, 'format' => 'json'
]);
$response = $request->inject();
```

And you'll get a `$response` from the other server.

### Model Priorty

You can set the `$priority` property of a model, and if you use the MongoDB adapter and PECL Mongo 1.3+, it will adjust the `writeConcern` and `readPreference` for the encapsulated collection accordingly.

```php
namespace App\Model;

class Test extends \Core\Blueprint\Model
{
	$priority = 2;
	$schema = [];
}
```

This is how a MongoDB adapter would react to the following priority levels:

- **Minimum** (1): writeConcern `0` (Fire and forget). Read Preference `Nearest`.
- **Normal** (2): writeConcern `1` (Wait for primary to write). Read Preference `Primary Preferred`.
- **Maximum** (3): writeConcern `majority` (Majority of servers must acknowledge). Read Preference `Primary`.

### Deployment Via Git

The framework has a cli tool that can help you deploy via git like this:

```bash
$ _cli/bin/rbhp deploy
```

The deployment will run you through configuration first. I'll write a more comprehensive guide perhaps some time in the future.

## Some other features that might be worthwhile to mention:

- Test Driven Development
- Documented with DocBlocks
- ViewModels — hence I use Mustache as the default templating engine.

## Installation

It uses composer, so you will have to do this first:

```bash
$ php composer.phar install
```

And then run some tests (Although at this stage, they won't pass unless you have MongoDB installed and password-less):

```bash
$ _cli/bin/rbhp test core
```

# License

_All files within this repository are licensed as follows:_

>	Copyright 2012 Nathan Kot
>
>	Licensed under the Apache License, Version 2.0 (the "License");
>	you may not use this file except in compliance with the License.
>	You may obtain a copy of the License at
>
>	http://www.apache.org/licenses/LICENSE-2.0
>
>	Unless required by applicable law or agreed to in writing, software
>	distributed under the License is distributed on an "AS IS" BASIS,
>	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
>	See the License for the specific language governing permissions and
>	limitations under the License.

