# TODO: Implement Supplier Name Field in Contracts

## ✅ Completed Tasks

### Database Changes
- [x] Migration created: `2026_01_15_113038_add_supplier_name_to_contracts_table.php`
- [x] Added supplier_name field (string, nullable) to contracts table

## 🛠️ Implementation Tasks

### Model Updates
- [ ] Add supplier_name to Contract model fillable array

### Controller Updates
- [ ] Update ContractController validation rules to include supplier_name
- [ ] Update ContractController store method to handle supplier_name
- [ ] Update ContractController update method to handle supplier_name
- [ ] Add API routes for supplier search (/api/suppliers/search and /api/suppliers/check)
- [ ] Implement searchSuppliers and checkSupplier methods in SupplierController

### View Updates
- [ ] Update contract show.blade.php to display supplier_name
- [ ] Update contract edit.blade.php to include supplier_name field with autocomplete

### Testing
- [ ] Test supplier search API endpoints
- [ ] Test contract creation with supplier selection
- [ ] Test contract editing with supplier changes
- [ ] Test contract display with supplier information

## 📋 Technical Details

- **New Field**: supplier_name (string, nullable) in contracts table
- **API Endpoints**:
  - GET /api/suppliers/search?q={query} - Search suppliers by name
  - GET /api/suppliers/check?name={name} - Check if supplier exists
- **Form Integration**: Autocomplete in create/edit forms
- **Display**: Show supplier name in contract details

## 🎯 Next Steps

1. Update Contract model
2. Update ContractController
3. Add API routes and methods
4. Update views
5. Test functionality
