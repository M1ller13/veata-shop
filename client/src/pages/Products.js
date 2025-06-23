import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useSearchParams } from 'react-router-dom';
import {
  Box,
  Container,
  Grid,
  Typography,
  Card,
  CardContent,
  CardMedia,
  CardActions,
  Button,
  Pagination,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  TextField,
  Drawer,
  IconButton,
  useTheme,
  useMediaQuery,
  Divider,
  Chip,
  Slider,
  FormGroup,
  FormControlLabel,
  Checkbox,
  CircularProgress
} from '@mui/material';
import {
  FilterList as FilterIcon,
  Close as CloseIcon,
  Search as SearchIcon
} from '@mui/icons-material';
import { fetchProducts, fetchCategories } from '../store/slices/productSlice';
import { Link as RouterLink } from 'react-router-dom';

const Products = () => {
  const dispatch = useDispatch();
  const [searchParams, setSearchParams] = useSearchParams();
  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down('sm'));

  const { products, totalPages, currentPage, loading } = useSelector((state) => state.products);
  const { categories } = useSelector((state) => state.categories);

  const [drawerOpen, setDrawerOpen] = useState(false);
  const [filters, setFilters] = useState({
    search: searchParams.get('search') || '',
    category: searchParams.get('category') || '',
    sort: searchParams.get('sort') || 'newest'
  });

  useEffect(() => {
    dispatch(fetchCategories());
  }, [dispatch]);

  useEffect(() => {
    const params = new URLSearchParams();
    if (filters.search) params.set('search', filters.search);
    if (filters.category) params.set('category', filters.category);
    if (filters.sort) params.set('sort', filters.sort);
    if (currentPage > 1) params.set('page', currentPage);

    dispatch(fetchProducts(Object.fromEntries(params)));
    setSearchParams(params);
  }, [dispatch, filters, currentPage, setSearchParams]);

  const handleFilterChange = (event) => {
    const { name, value } = event.target;
    setFilters((prev) => ({ ...prev, [name]: value }));
  };

  const handlePageChange = (event, value) => {
    setFilters((prev) => ({ ...prev, page: value }));
  };

  const handlePriceRangeChange = (event, newValue) => {
    setFilters(prev => ({ ...prev, priceRange: newValue }));
  };

  const handlePriceRangeChangeCommitted = () => {
    const params = new URLSearchParams(searchParams);
    params.set('minPrice', filters.priceRange[0]);
    params.set('maxPrice', filters.priceRange[1]);
    setSearchParams(params);
  };

  const clearFilters = () => {
    setFilters({
      search: '',
      category: '',
      sort: 'newest'
    });
    setSearchParams({});
  };

  const FilterDrawer = () => (
    <Drawer
      anchor="left"
      open={drawerOpen}
      onClose={() => setDrawerOpen(false)}
      sx={{
        '& .MuiDrawer-paper': {
          width: isMobile ? '100%' : 300,
          p: 2
        }
      }}
    >
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
        <Typography variant="h6">Filters</Typography>
        <IconButton onClick={() => setDrawerOpen(false)}>
          <CloseIcon />
        </IconButton>
      </Box>
      <Divider sx={{ mb: 2 }} />

      {/* Category Filter */}
      <FormControl fullWidth sx={{ mb: 2 }}>
        <InputLabel>Category</InputLabel>
        <Select
          name="category"
          value={filters.category}
          label="Category"
          onChange={handleFilterChange}
        >
          <MenuItem value="">All Categories</MenuItem>
          {categories.map((category) => (
            <MenuItem key={category._id} value={category._id}>
              {category.name}
            </MenuItem>
          ))}
        </Select>
      </FormControl>

      {/* Price Range Filter */}
      <Typography gutterBottom>Price Range</Typography>
      <Slider
        value={filters.priceRange}
        onChange={handlePriceRangeChange}
        onChangeCommitted={handlePriceRangeChangeCommitted}
        valueLabelDisplay="auto"
        min={0}
        max={1000}
        sx={{ mb: 2 }}
      />
      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 2 }}>
        <Typography>${filters.priceRange[0]}</Typography>
        <Typography>${filters.priceRange[1]}</Typography>
      </Box>

      {/* Sort Filter */}
      <FormControl fullWidth sx={{ mb: 2 }}>
        <InputLabel>Sort By</InputLabel>
        <Select
          name="sort"
          value={filters.sort}
          label="Sort By"
          onChange={handleFilterChange}
        >
          <MenuItem value="newest">Newest</MenuItem>
          <MenuItem value="price_asc">Price: Low to High</MenuItem>
          <MenuItem value="price_desc">Price: High to Low</MenuItem>
        </Select>
      </FormControl>

      <Button
        variant="outlined"
        fullWidth
        onClick={clearFilters}
        sx={{ mt: 2 }}
      >
        Clear Filters
      </Button>
    </Drawer>
  );

  if (loading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="60vh">
        <CircularProgress />
      </Box>
    );
  }

  return (
    <Container maxWidth="lg">
      <Box sx={{ mb: 4 }}>
        <Typography variant="h4" component="h1" gutterBottom>
          Products
        </Typography>

        {/* Filters */}
        <Grid container spacing={2} sx={{ mb: 4 }}>
          <Grid item xs={12} sm={4}>
            <TextField
              fullWidth
              label="Search"
              name="search"
              value={filters.search}
              onChange={handleFilterChange}
            />
          </Grid>
          <Grid item xs={12} sm={4}>
            <FormControl fullWidth>
              <InputLabel>Category</InputLabel>
              <Select
                name="category"
                value={filters.category}
                label="Category"
                onChange={handleFilterChange}
              >
                <MenuItem value="">All Categories</MenuItem>
                {categories.map((category) => (
                  <MenuItem key={category._id} value={category._id}>
                    {category.name}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>
          <Grid item xs={12} sm={4}>
            <FormControl fullWidth>
              <InputLabel>Sort By</InputLabel>
              <Select
                name="sort"
                value={filters.sort}
                label="Sort By"
                onChange={handleFilterChange}
              >
                <MenuItem value="newest">Newest</MenuItem>
                <MenuItem value="price_asc">Price: Low to High</MenuItem>
                <MenuItem value="price_desc">Price: High to Low</MenuItem>
              </Select>
            </FormControl>
          </Grid>
        </Grid>

        {/* Products Grid */}
        <Grid container spacing={4}>
          {products.map((product) => (
            <Grid item key={product._id} xs={12} sm={6} md={4} lg={3}>
              <Card>
                <CardMedia
                  component="img"
                  height="200"
                  image={product.image}
                  alt={product.name}
                />
                <CardContent>
                  <Typography gutterBottom variant="h6" component="h3">
                    {product.name}
                  </Typography>
                  <Typography variant="body2" color="text.secondary">
                    ${product.price}
                  </Typography>
                </CardContent>
                <CardActions>
                  <Button
                    component={RouterLink}
                    to={`/products/${product._id}`}
                    size="small"
                    color="primary"
                  >
                    View Details
                  </Button>
                </CardActions>
              </Card>
            </Grid>
          ))}
        </Grid>

        {/* Pagination */}
        {totalPages > 1 && (
          <Box sx={{ mt: 4, display: 'flex', justifyContent: 'center' }}>
            <Pagination
              count={totalPages}
              page={currentPage}
              onChange={handlePageChange}
              color="primary"
            />
          </Box>
        )}
      </Box>

      {/* Mobile Filter Drawer */}
      <FilterDrawer />
    </Container>
  );
};

export default Products; 