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

/**
 * Moves all selected items as one block while preserving their relative order.
 *
 * @param {Array<{ id: string }>} items
 * @param {Set<string>} selectedIds
 * @param {number} dragFromIndex
 * @param {number} dragToIndex
 * @returns {Array<{ id: string }>}
 */
export function reorderSelectedBlock(items, selectedIds, dragFromIndex, dragToIndex) {
  if (selectedIds.size <= 1) {
    return items
  }

  const draggedId = items[dragFromIndex]?.id
  if (!draggedId || !selectedIds.has(draggedId)) {
    return items
  }

  const { block, rest } = splitSelectedItems(items, selectedIds)

  let insertAt = 0
  for (let i = 0; i < dragToIndex; i++) {
    if (!selectedIds.has(items[i].id)) {
      insertAt++
    }
  }

  if (dragToIndex > dragFromIndex) {
    for (let i = dragFromIndex + 1; i < dragToIndex; i++) {
      if (selectedIds.has(items[i].id)) {
        insertAt++
      }
    }
  }

  return [...rest.slice(0, insertAt), ...block, ...rest.slice(insertAt)]
}
