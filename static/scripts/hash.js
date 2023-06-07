/**
 * Sha512 hash function
 * @param {string} str
 * @returns {Promise<string>}
 */
export async function sha512(str) {
	// Convert to UTF-8 string before hashing
	const encodedInput = new TextEncoder().encode(str);

	// Get crypto hash algorithm (SHA-512)
	const crypto = await window.crypto;
	const hash = await crypto.subtle.digest('SHA-512', encodedInput);

	// Convert to hex string
	return Array.from(new Uint8Array(hash)).map(b => b.toString(16).padStart(2, '0')).join('');
}
