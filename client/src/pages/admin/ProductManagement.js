import React, { useState, useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import {
  Box,
  Container,
  Paper,
  Typography,
  Grid,
  TextField,
  Button,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Chip,
  IconButton,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TablePagination,
  InputAdornment,
  Alert,
  Snackbar,
  Tooltip,
  Card,
  CardContent
} from '@mui/material';
import {
  Add as AddIcon,
  Edit as EditIcon,
  Delete as DeleteIcon,
  Search as SearchIcon,
  Image as ImageIcon,
  Save as SaveIcon,
  Cancel as CancelIcon
} from '@mui/icons-material';
import { fetchProducts, createProduct, updateProduct, deleteProduct } from '../../store/slices/productSlice';
import ProductForm from './ProductForm';

const ProductManagement = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { products, loading, error } = useSelector((state) => state.products);

  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [searchQuery, setSearchQuery] = useState('');
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [snackbar, setSnackbar] = useState({ open: false, message: '', severity: 'success' });

  const [formData, setFormData] = useState({
    name: '',
    description: '',
    category: '',
    price: '',
    stock: '',
    unit: '',
    dimensions: '',
    weight: '',
    material: '',
    brand: '',
    specifications: '',
    images: [],
    features: [],
    isActive: true
  });

  const [categories, setCategories] = useState([]);
  const [newCategory, setNewCategory] = useState('');
  const [editCategory, setEditCategory] = useState(null);

  useEffect(() => {
    dispatch(fetchProducts());
  }, [dispatch]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (selectedProduct) {
        await dispatch(updateProduct({ id: selectedProduct._id, ...formData }));
        setSnackbar({
          open: true,
          message: 'Product updated successfully',
          severity: 'success'
        });
      } else {
        await dispatch(createProduct(formData));
        setSnackbar({
          open: true,
          message: 'Product created successfully',
          severity: 'success'
        });
      }
      handleCloseDialog();
    } catch (err) {
      setSnackbar({
        open: true,
        message: err.message || 'An error occurred',
        severity: 'error'
      });
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this product?')) {
      try {
        await dispatch(deleteProduct(id));
        setSnackbar({
          open: true,
          message: 'Product deleted successfully',
          severity: 'success'
        });
      } catch (err) {
        setSnackbar({
          open: true,
          message: err.message || 'An error occurred',
          severity: 'error'
        });
      }
    }
  };

  const handleEdit = (product) => {
    setSelectedProduct(product);
    setFormData({
      name: product.name,
      description: product.description,
      category: product.category,
      price: product.price,
      stock: product.stock,
      unit: product.unit,
      dimensions: product.dimensions,
      weight: product.weight,
      material: product.material,
      brand: product.brand,
      specifications: product.specifications,
      images: product.images,
      features: product.features,
      isActive: product.isActive
    });
    setOpenDialog(true);
  };

  const handleCloseDialog = () => {
    setOpenDialog(false);
    setSelectedProduct(null);
    setFormData({
      name: '',
      description: '',
      category: '',
      price: '',
      stock: '',
      unit: '',
      dimensions: '',
      weight: '',
      material: '',
      brand: '',
      specifications: '',
      images: [],
      features: [],
      isActive: true
    });
  };

  const filteredProducts = products.filter(product =>
    product.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
    product.category.toLowerCase().includes(searchQuery.toLowerCase()) ||
    product.brand.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const handleCategoryAdd = () => {
    if (newCategory.trim()) {
      setCategories([...categories, newCategory.trim()]);
      setNewCategory('');
      setSnackbar({
        open: true,
        message: 'Категория добавлена',
        severity: 'success'
      });
    }
  };

  const handleCategoryEdit = (oldCategory) => {
    setEditCategory(oldCategory);
    setNewCategory(oldCategory);
  };

  const handleCategorySave = () => {
    if (newCategory.trim()) {
      setCategories(categories.map(cat => 
        cat === editCategory ? newCategory.trim() : cat
      ));
      setEditCategory(null);
      setNewCategory('');
      setSnackbar({
        open: true,
        message: 'Категория обновлена',
        severity: 'success'
      });
    }
  };

  const handleCategoryDelete = (category) => {
    setCategories(categories.filter(cat => cat !== category));
    setSnackbar({
      open: true,
      message: 'Категория удалена',
      severity: 'success'
    });
  };

  const ProductFormComponent = () => (
    <ProductForm
      open={openDialog}
      onClose={handleCloseDialog}
      onSubmit={handleSubmit}
      initialData={selectedProduct}
      categories={categories}
    />
  );

  return (
    <Container maxWidth="lg">
      <Box sx={{ mt: 4, mb: 4 }}>
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
          <Typography variant="h4">
            Product Management
          </Typography>
          <Button
            variant="contained"
            startIcon={<AddIcon />}
            onClick={() => {
              setSelectedProduct(null);
              setFormData({
                name: '',
                description: '',
                category: '',
                price: '',
                stock: '',
                unit: '',
                dimensions: '',
                weight: '',
                material: '',
                brand: '',
                specifications: '',
                images: [],
                features: [],
                isActive: true
              });
              setOpenDialog(true);
            }}
          >
            Add New Product
          </Button>
        </Box>

        <Paper sx={{ p: 2, mb: 3 }}>
          <TextField
            fullWidth
            placeholder="Search products by name, category, or brand..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            InputProps={{
              startAdornment: (
                <InputAdornment position="start">
                  <SearchIcon />
                </InputAdornment>
              )
            }}
          />
        </Paper>

        <Grid container spacing={3}>
          {/* Category Management */}
          <Grid item xs={12} md={4}>
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  Управление категориями
                </Typography>
                <Box sx={{ mb: 2 }}>
                  <TextField
                    fullWidth
                    label="Новая категория"
                    value={newCategory}
                    onChange={(e) => setNewCategory(e.target.value)}
                    sx={{ mb: 1 }}
                  />
                  <Button
                    variant="contained"
                    color="primary"
                    onClick={editCategory ? handleCategorySave : handleCategoryAdd}
                    startIcon={editCategory ? <SaveIcon /> : <AddIcon />}
                  >
                    {editCategory ? 'Сохранить' : 'Добавить'}
                  </Button>
                  {editCategory && (
                    <Button
                      variant="outlined"
                      color="secondary"
                      onClick={() => {
                        setEditCategory(null);
                        setNewCategory('');
                      }}
                      startIcon={<CancelIcon />}
                      sx={{ ml: 1 }}
                    >
                      Отмена
                    </Button>
                  )}
                </Box>
                <TableContainer>
                  <Table size="small">
                    <TableHead>
                      <TableRow>
                        <TableCell>Категория</TableCell>
                        <TableCell align="right">Действия</TableCell>
                      </TableRow>
                    </TableHead>
                    <TableBody>
                      {categories.map((category) => (
                        <TableRow key={category}>
                          <TableCell>{category}</TableCell>
                          <TableCell align="right">
                            <IconButton
                              size="small"
                              onClick={() => handleCategoryEdit(category)}
                            >
                              <EditIcon />
                            </IconButton>
                            <IconButton
                              size="small"
                              onClick={() => handleCategoryDelete(category)}
                            >
                              <DeleteIcon />
                            </IconButton>
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </TableContainer>
              </CardContent>
            </Card>
          </Grid>

          {/* Product Management */}
          <Grid item xs={12} md={8}>
            <Card>
              <CardContent>
                <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 2 }}>
                  <Typography variant="h6">
                    Управление товарами
                  </Typography>
                  <Button
                    variant="contained"
                    color="primary"
                    startIcon={<AddIcon />}
                    onClick={() => {
                      setSelectedProduct(null);
                      setFormData({
                        name: '',
                        description: '',
                        category: '',
                        price: '',
                        stock: '',
                        unit: '',
                        dimensions: '',
                        weight: '',
                        material: '',
                        brand: '',
                        specifications: '',
                        images: [],
                        features: [],
                        isActive: true
                      });
                      setOpenDialog(true);
                    }}
                  >
                    Добавить товар
                  </Button>
                </Box>
                <TableContainer>
                  <Table>
                    <TableHead>
                      <TableRow>
                        <TableCell>Название</TableCell>
                        <TableCell>Категория</TableCell>
                        <TableCell>Цена</TableCell>
                        <TableCell>Наличие</TableCell>
                        <TableCell align="right">Действия</TableCell>
                      </TableRow>
                    </TableHead>
                    <TableBody>
                      {filteredProducts
                        .slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                        .map((product) => (
                          <TableRow key={product._id}>
                            <TableCell>{product.name}</TableCell>
                            <TableCell>{product.category}</TableCell>
                            <TableCell>${product.price}</TableCell>
                            <TableCell>
                              <Chip
                                label={product.isActive ? 'Active' : 'Inactive'}
                                color={product.isActive ? 'success' : 'default'}
                                size="small"
                              />
                            </TableCell>
                            <TableCell>
                              <Tooltip title="Edit">
                                <IconButton
                                  size="small"
                                  onClick={() => handleEdit(product)}
                                >
                                  <EditIcon />
                                </IconButton>
                              </Tooltip>
                              <Tooltip title="Delete">
                                <IconButton
                                  size="small"
                                  onClick={() => handleDelete(product._id)}
                                >
                                  <DeleteIcon />
                                </IconButton>
                              </Tooltip>
                            </TableCell>
                          </TableRow>
                        ))}
                    </TableBody>
                  </Table>
                  <TablePagination
                    rowsPerPageOptions={[5, 10, 25]}
                    component="div"
                    count={filteredProducts.length}
                    rowsPerPage={rowsPerPage}
                    page={page}
                    onPageChange={(e, newPage) => setPage(newPage)}
                    onRowsPerPageChange={(e) => {
                      setRowsPerPage(parseInt(e.target.value, 10));
                      setPage(0);
                    }}
                  />
                </TableContainer>
              </CardContent>
            </Card>
          </Grid>
        </Grid>

        <ProductFormComponent />

        <Snackbar
          open={snackbar.open}
          autoHideDuration={6000}
          onClose={() => setSnackbar({ ...snackbar, open: false })}
        >
          <Alert
            onClose={() => setSnackbar({ ...snackbar, open: false })}
            severity={snackbar.severity}
            sx={{ width: '100%' }}
          >
            {snackbar.message}
          </Alert>
        </Snackbar>
      </Box>
    </Container>
  );
};

export default ProductManagement; 