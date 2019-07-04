import nodeResolve from 'rollup-plugin-node-resolve';
import commonjs from 'rollup-plugin-commonjs';
import buble from 'rollup-plugin-buble';
import sass from 'rollup-plugin-sass';
import { uglify } from 'rollup-plugin-uglify';
import es3 from 'rollup-plugin-es3';
import svgi from 'rollup-plugin-svgi';
import typescript from 'rollup-plugin-typescript';

export default {
	input: 'assets/Chat/chat.tsx',
	output: {
		file: 'assets/chat.bundle.js',
		format: 'iife'
	},
	external: [],
	plugins: [
		typescript(),
		svgi({
			options: {
				jsx: 'preact',
			}
		}),
		sass({
			output: 'assets/chat.bundle.css',
		}),
		buble({
			jsx: 'h',
			objectAssign: 'Object.assign'
		}),
		nodeResolve({
			browser: true
		}),
		commonjs({
		}),
		uglify({
			output: { comments: false },
			mangle: {
				toplevel: true,
				properties: { regex: /^_/ }
			}
		}),
		es3()
	]
};