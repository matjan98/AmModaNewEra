/**
 * @param {Array<{ id: string }>} items
 * @param {Set<string>} selectedIds
 * @returns {{ block: Array<{ id: string }>, rest: Array<{ id: string }> }}
 */
function splitSelectedItems(items, selectedIds) {
  const block = items.filter((item) => selectedIds.has(item.id))
  const rest = items.filter((item) => !selectedIds.has(item.id))
  return { block, rest }
}

/**
 * @param {Array<{ id: string }>} items
 * @param {Set<string>} selectedIds
 * @returns {Array<{ id: string }>}
 */
export function moveItemsToTop(items, selectedIds) {
  if (selectedIds.size === 0) {
    return items
  }

  const { block, rest } = splitSelectedItems(items, selectedIds)
  return [...block, ...rest]
}

/**
 * @param {Array<{ id: string }>} items
 * @param {Set<string>} selectedIds
 * @returns {Array<{ id: string }>}
 */
export function moveItemsToBottom(items, selectedIds) {
  if (selectedIds.size === 0) {
    return items
  }

  const { block, rest } = splitSelectedItems(items, selectedIds)
  return [...rest, ...block]
}
