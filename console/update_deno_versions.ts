const res = await fetch(
    `https://cdn.deno.land/deno/meta/versions.json`,
);
const version = await res.json();
const latestVersion = version.latest.replace("v", "");
let dockerFileContent = new TextDecoder().decode(Deno.readFileSync("./.docker/drash.dockerfile"))
dockerFileContent = dockerFileContent.replace(/-s v[0-9].[0-9].[0-9]/, `-s v${latestVersion}`)
Deno.writeFileSync("./.docker/drash.dockerfile", new TextEncoder().encode(dockerFileContent))
