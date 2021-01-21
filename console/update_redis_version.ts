const res = await fetch(
    `https://api.github.com/repos/redis/redis/releases/latest`,
);
const json = await res.json();
const latestMajorVersion = json.tag_name.split('.')[0]
let content = new TextDecoder().decode(Deno.readFileSync("docker-compose.yml"))
content = content.replace(/image: redis:[0-9]/, `image: redis:${latestMajorVersion}`)
Deno.writeFileSync("docker-compose.yml", new TextEncoder().encode(content))