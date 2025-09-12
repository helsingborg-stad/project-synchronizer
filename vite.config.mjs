import { createViteConfig } from "vite-config-factory";

const entries = {
	"js/empty": "./source/js/empty.ts",
};

export default createViteConfig(entries, {
	outDir: "dist",
	manifestFile: "manifest.json",
});
