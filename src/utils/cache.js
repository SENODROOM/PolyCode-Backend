const { CACHE_TTL } = require("../config/constants");

/**
 * Enhanced Caching System
 */
const cache = {
  languages: { data: null, timestamp: 0 },
  trees: new Map(),
  stats: new Map(),
  documents: new Map(),
  fileIndex: new Map(),
};

/**
 * Memory management - clear old cache entries periodically
 */
setInterval(() => {
  const now = Date.now();
  for (const [key, map] of [
    ["trees", cache.trees],
    ["stats", cache.stats],
    ["documents", cache.documents],
    ["fileIndex", cache.fileIndex],
  ]) {
    for (const [innerKey, value] of map.entries()) {
      if (now - value.timestamp >= CACHE_TTL) {
        map.delete(innerKey);
      }
    }
  }
}, CACHE_TTL);

/**
 * Get item from cache if not expired
 * @param {Object} cacheObj - Cache object (Map or object with data property)
 * @param {string|null} key - Cache key (null for non-Map caches)
 * @returns {*} Cached data or null if expired/missing
 */
function getFromCache(cacheObj, key) {
  const item = key ? cacheObj.get(key) : cacheObj.data;
  if (item && Date.now() - item.timestamp < CACHE_TTL) {
    return item.data;
  }
  return null;
}

/**
 * Set item in cache with timestamp
 * @param {Object} cacheObj - Cache object (Map or object with data property)
 * @param {string|null} key - Cache key (null for non-Map caches)
 * @param {*} data - Data to cache
 */
function setInCache(cacheObj, key, data) {
  const item = { data, timestamp: Date.now() };
  if (key) {
    cacheObj.set(key, item);
  } else {
    cacheObj.data = item;
  }
}

/**
 * Clear all caches
 */
function clearCache() {
  cache.languages = { data: null, timestamp: 0 };
  cache.trees.clear();
  cache.stats.clear();
  cache.documents.clear();
  cache.fileIndex.clear();
}

module.exports = {
  cache,
  getFromCache,
  setInCache,
  clearCache,
};
