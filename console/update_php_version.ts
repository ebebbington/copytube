const res = await fetch("https://www.php.net/releases/?json")
const json = await res.json()
const versions = Object.keys(json).map(key => Number(key))
const latestMajorVersion = Math.max.apply(Math, versions); // 8
const latestVersion = json[latestMajorVersion].version // 8.0.1
const latestMinorVersion = latestVersion.substring(0, latestVersion.length - 1)

const phpDockerfileContent = new TextDecoder().decode(Deno.readFileSync("./.docker/phpfpm.dockerfile"))
const splitDockerfileContent = phpDockerfileContent.split("\n");
splitDockerfileContent[0] = `FROM php:${latestVersion}-fpm`
Deno.writeFileSync("./.docker/phpfpm.dockerfile", new TextEncoder().encode(splitDockerfileContent.join("\n")))

let masterWorkflowContent = new TextDecoder().decode(Deno.readFileSync("./.github/workflows/master.yml"))
masterWorkflowContent = masterWorkflowContent.replace(/php-version: [0-9.0-9]/g, `php-version: ${latestMinorVersion}`)
Deno.writeFileSync("./.github/workflow/master.yml", new TextEncoder().encode(masterWorkflowContent))

let upgraderWorkflowContent = new TextDecoder().decode(Deno.readFileSync("./.github/workflows/upgrader.yml"))
upgraderWorkflowContent = upgraderWorkflowContent.replace(/php-version: [0-9.0-9]/g, `php-version: ${latestMinorVersion}`)
Deno.writeFileSync("./.github/workflow/upgrader.yml", new TextEncoder().encode(upgraderWorkflowContent))

